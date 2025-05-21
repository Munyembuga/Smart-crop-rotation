@extends('layouts.admin')

@section('title', 'User Management - Admin Dashboard')

@section('content')
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h2 style="margin: 0;">User Management</h2>
        <div class="actions">
            <a href="{{ route('admin.users.create') }}" class="btn" style="display: flex; align-items: center; background-color: #0ac15e; color: white; margin-right: 10px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right: 6px;">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New User
            </a>
            <!-- <a href="{{ route('admin.roles.create') }}" class="btn" style="background-color: #303f9f; color: white;">Create Role</a> -->
        </div>
    </div>

    <div class="recent-users">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach(\App\Models\User::latest()->paginate(10) as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role->name ?? 'No Role' }}</td>
                    <td>{{ $user->status }}</td>
                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                    <td>
                        <div style="display: flex; gap: 5px;">
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn" style="padding: 6px 12px; font-size: 0.875rem; background-color: #0ac15e; color: white;">Edit</a>
                            <a href="{{ route('admin.users.show', $user->id) }}" class="btn" style="padding: 6px 12px; font-size: 0.875rem; background-color: #0ac15e; color: white;">View</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
        {{ \App\Models\User::latest()->paginate(10)->links() }}
    </div>
@endsection