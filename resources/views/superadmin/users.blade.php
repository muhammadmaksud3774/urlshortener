@extends('superadmin.layout')

@section('content')
<h2>Users</h2>

{{-- Create User Form --}}
<form method="POST" action="{{ route('superadmin.users.store') }}" class="mb-3">
    @csrf
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    {{-- Company Dropdown --}}
    <select name="company_id" required>
        <option value="">Select Company</option>
        @if(auth()->user()->hasRole('SuperAdmin'))
            {{-- SuperAdmin can select any company --}}
            @foreach($companies as $company)
                <option value="{{ $company->id }}">{{ $company->name }}</option>
            @endforeach
        @else
            {{-- Admin can only select their own company --}}
            <option value="{{ auth()->user()->company->id }}">{{ auth()->user()->company->name }}</option>
        @endif
    </select>

    {{-- Role Dropdown --}}
    <select name="role" required>
        <option value="">Select Role</option>
        @foreach($roles as $role)
            @if(auth()->user()->hasRole('Admin') && $role->name == 'Admin')
                {{-- Admin cannot create another Admin --}}
                @continue
            @endif
            <option value="{{ $role->name }}">{{ $role->name }}</option>
        @endforeach
    </select>

    <button class="btn btn-primary btn-sm">Create User</button>
</form>

{{-- Users Table --}}
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Role</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->id }}</td>
            <td>{{ $user->name }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->company->name ?? '-' }}</td>
            <td>{{ $user->roles->pluck('name')->join(', ') }}</td>
            <td>
                <form method="POST" action="{{ route('superadmin.users.delete', $user) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
