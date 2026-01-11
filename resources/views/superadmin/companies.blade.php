@extends('superadmin.layout')

@section('content')
<h2>Companies</h2>

<form method="POST" action="{{ route('superadmin.companies.store') }}" class="mb-3">
    @csrf
    <input type="text" name="name" placeholder="New Company Name" required>
    <button class="btn btn-primary btn-sm">Create Company</button>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        @foreach($companies as $company)
        <tr>
            <td>{{ $company->id }}</td>
            <td>{{ $company->name }}</td>
            <td>
                <form method="POST" action="{{ route('superadmin.companies.delete', $company) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this company?')">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
