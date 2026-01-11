<?php
namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\User;
use App\Models\Role;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class SuperAdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function companiesView()
    {
		$user = auth()->user();
		if (!$user->hasRole('SuperAdmin')) {
			abort(403, 'Unauthorized');
		}
        $companies = Company::with('users.roles')->get();
        return view('superadmin.companies', compact('companies'));
    }

    public function createCompany(Request $request)
    {
        $user = auth()->user();
		if (!$user->hasRole('SuperAdmin')) {
			abort(403, 'Unauthorized');
		}
        $request->validate(['name' => 'required|unique:companies,name']);
        Company::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Company created successfully');
    }

    public function deleteCompany(Company $company)
    {
        $user = auth()->user();
		if (!$user->hasRole('SuperAdmin')) {
			abort(403, 'Unauthorized');
		}
        $company->delete();

        return redirect()->back()->with('success', 'Company deleted successfully');
    }

    public function usersView()
	{
		$authUser = auth()->user();

		if (!$authUser->hasRole('SuperAdmin') && !$authUser->hasRole('Admin')) {
			abort(403, 'Unauthorized');
		}

		$allowedRoles = ['Admin', 'Member'];

		if ($authUser->hasRole('SuperAdmin')) {
			$users = User::with('company', 'roles')
				->whereHas('roles', function ($q) use ($allowedRoles) {
					$q->whereIn('name', $allowedRoles);
				})
				->get();
			$companies = Company::all();
		} else {
			$users = User::with('company', 'roles')
				->whereHas('roles', function ($q) use ($allowedRoles) {
					$q->whereIn('name', $allowedRoles);
				})
				->where('company_id', $authUser->company_id)
				->get();
			$companies = Company::where('id', $authUser->company_id)->get();
		}

		$roles = Role::whereIn('name', $allowedRoles)->get();
		return view('superadmin.users', compact('users', 'companies', 'roles'));
	}


    public function createUser(Request $request)
    {
        $authUser = auth()->user();
		if (!$authUser->hasRole('SuperAdmin') && !$authUser->hasRole('Admin')) {
			abort(403, 'Unauthorized');
		}
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'company_id' => 'required|exists:companies,id',
            'role' => 'required|in:Admin,Member',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'company_id' => $request->company_id
        ]);

        $role = Role::where('name', $request->role)->first();
        $user->roles()->attach($role);

        return redirect()->back()->with('success', 'User created successfully');
    }

    public function deleteUser(User $user)
    {
        $user = auth()->user();
		if (!$user->hasRole('SuperAdmin')) {
			abort(403, 'Unauthorized');
		}
        if ($user->hasRole('SuperAdmin')) {
            return redirect()->back()->with('error', 'Cannot delete SuperAdmin');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully');
    }
	
	public function invitationsView()
    {
        $user = auth()->user();

        if ($user->hasRole('SuperAdmin')) {
            $invitations = Invitation::with('company', 'inviter')->get();
        } elseif ($user->hasRole('Admin')) {
            $invitations = Invitation::with('company', 'inviter')
                ->where('company_id', $user->company_id)
                ->get();
        } else {
            abort(403, 'Unauthorized');
        }

        return view('superadmin.invitations', compact('invitations'));
    }

    public function sendInvitation(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'role' => 'required|in:Admin,Member',
            'company_id' => 'required|exists:companies,id',
        ]);

        $user = auth()->user();
        $company = Company::findOrFail($request->company_id);

        if ($user->hasRole('SuperAdmin')) {
            if ($company->users()->count() > 0) {
                return back()->withErrors(['company_id' => 'SuperAdmin can only invite Admin to a new company.']);
            }
            if ($request->role !== 'Admin') {
                return back()->withErrors(['role' => 'SuperAdmin can only invite Admin in a new company.']);
            }
        }

        if ($user->hasRole('Admin')) {
            if ($user->company_id !== $company->id) {
                return back()->withErrors(['company_id' => 'Admin can only invite users in their own company.']);
            }
        }

        $invitation = Invitation::create([
            'company_id' => $company->id,
            'invited_by' => $user->id,
            'email' => $request->email,
            'role' => $request->role,
            'token' => Str::random(32),
        ]);

        return back()->with('success', 'Invitation sent successfully.');
    }

    public function acceptInvitation($token)
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->accepted) {
            return redirect('/login')->withErrors('Invitation already used.');
        }
		
		if (\App\Models\User::where('email', $invitation->email)->exists()) {
			return redirect('/login')
				->withErrors('An account with this email already exists. Please login.');
		}

        return view('superadmin.accept', compact('invitation'));
    }

    public function registerFromInvitation(Request $request, $token)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $invitation = Invitation::where('token', $token)->firstOrFail();

        if ($invitation->accepted) {
            return redirect('/login')->withErrors('Invitation already used.');
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $invitation->email,
            'password' => bcrypt($request->password),
            'company_id' => $invitation->company_id,
        ]);

        $role = \App\Models\Role::where('name', $invitation->role)->first();
        $user->roles()->attach($role->id);

        $invitation->update(['accepted' => true]);

        return redirect('/login')->with('success', 'Account created. You can now login.');
    }

}
?>