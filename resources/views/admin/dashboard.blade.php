@extends('layouts.admin')

@section('title', 'Admin Dashboard - Smart Crop Rotation')

@section('content')
    <h2>System Admin Dashboard</h2>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Users</h3>
            <div class="number">{{ \App\Models\User::count() }}</div>
        </div>
        <div class="stat-card">
            <h3>Farmers</h3>
            <div class="number">{{ \App\Models\User::where('role_id', 1)->count() }}</div>
        </div>
        <div class="stat-card">
            <h3>Total Farms</h3>
            <div class="number">{{ \App\Models\Farm::count() }}</div>
        </div>
        <div class="stat-card">
            <h3>Administrators</h3>
            <div class="number">{{ \App\Models\User::where('role_id', '!=', 1)->count() }}</div>
        </div>
    </div>

    <div class="actions">
        <a href="{{ route('admin.users.create') }}" class="btn">Add New User</a>
        <a href="{{ route('admin.roles.create') }}" class="btn">Create Role</a>
    </div>

    <div class="recent-users">
        <h3>Recent Users</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered On</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\User::latest()->take(5)->get() as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->name ?? 'No Role' }}</td>
                    <td>{{ $user->status }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
