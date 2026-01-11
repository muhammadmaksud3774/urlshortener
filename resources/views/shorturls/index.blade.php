@extends('superadmin.layout')

@section('content')
<h2>Short URLs</h2>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- Create Form (Admin + Member only) --}}
@if(!auth()->user()->hasRole('SuperAdmin'))
<form method="POST" action="{{ route('shorturls.store') }}" class="mb-3">
    @csrf
    <input type="url" name="original_url" placeholder="Enter full URL" required>
    <button class="btn btn-primary btn-sm">Create</button>
</form>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Short URL</th>
            <th>Original URL</th>
            <th>Company</th>
            <th>Created By</th>
            <th>Clicks</th>
        </tr>
    </thead>
    <tbody>
        @foreach($urls as $url)
        <tr>
            <td>{{ $url->id }}</td>
            <td>
                <a href="{{ url('/s/'.$url->short_code) }}" target="_blank">
                    {{ url('/s/'.$url->short_code) }}
                </a>
            </td>
            <td>{{ $url->original_url }}</td>
            <td>{{ $url->company->name }}</td>
            <td>{{ $url->user->name }}</td>
            <td>{{ $url->clicks }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
