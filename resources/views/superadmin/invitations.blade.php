@extends('superadmin.layout')

@section('content')
<h2>Invitations</h2>

{{-- Flash messages --}}
@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- ===================== --}}
{{-- Send Invitation Form --}}
{{-- ===================== --}}
<div class="card mb-4">
    <div class="card-header">Send Invitation</div>
    <div class="card-body">
        <form method="POST" action="{{ route('invitations.send') }}">
            @csrf

            <div class="mb-2">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            {{-- Company --}}
            <div class="mb-2">
                <label>Company</label>
                <select name="company_id" class="form-control" required>
                    @if(auth()->user()->hasRole('SuperAdmin'))
                        <option value="">Select Company</option>
                        @foreach(\App\Models\Company::all() as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    @else
                        <option value="{{ auth()->user()->company_id }}">
                            {{ auth()->user()->company->name }}
                        </option>
                    @endif
                </select>
            </div>

            {{-- Role --}}
            <div class="mb-3">
                <label>Role</label>
                <select name="role" class="form-control" required>
                    @if(auth()->user()->hasRole('SuperAdmin'))
                        {{-- SuperAdmin can invite only Admin --}}
                        <option value="Admin">Admin</option>
                    @else
                        {{-- Admin can invite Admin or Member --}}
                        <option value="Admin">Admin</option>
                        <option value="Member">Member</option>
                    @endif
                </select>
            </div>

            <button class="btn btn-primary">Send Invitation</button>
        </form>
    </div>
</div>

{{-- ================= --}}
{{-- Invitations List --}}
{{-- ================= --}}
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Company</th>
            <th>Role</th>
            <th>Invited By</th>
            <th>Status</th>
            <th>Invite Link</th>
        </tr>
    </thead>
    <tbody>
        @foreach($invitations as $invite)
            <tr>
                <td>{{ $invite->id }}</td>
                <td>{{ $invite->email }}</td>
                <td>{{ $invite->company->name }}</td>
                <td>{{ $invite->role }}</td>
                <td>{{ $invite->inviter->name ?? '-' }}</td>
                <td>
                    @if($invite->accepted)
                        <span class="badge bg-success">Accepted</span>
                    @else
                        <span class="badge bg-warning">Pending</span>
                    @endif
                </td>
                <td>
                    @if(!$invite->accepted)
                        <a class="badge bg-success" href="{{ url('/invitations/accept/'.$invite->token) }}" target="_blank">Accept</a>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
@endsection
