@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Role Details: {{ $role->display_name }}</h5>
                    <div>
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-edit"></i> Edit Role
                        </a>
                        <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Roles
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th style="width: 30%">Name</th>
                                    <td>{{ $role->name }}</td>
                                </tr>
                                <tr>
                                    <th>Display Name</th>
                                    <td>{{ $role->display_name }}</td>
                                </tr>
                                <tr>
                                    <th>Description</th>
                                    <td>{{ $role->description ?: 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if ($role->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Created At</th>
                                    <td>{{ $role->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated At</th>
                                    <td>{{ $role->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Users with this Role</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($role->users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center">No users assigned to this role.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <h6>Permissions</h6>
                    <div class="row">
                        @foreach ($permissions as $module => $modulePermissions)
                            <div class="col-md-6 mb-3">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <strong>{{ ucwords(str_replace('_', ' ', $module)) }}</strong>
                                    </div>
                                    <div class="card-body">
                                        <ul class="list-group">
                                            @foreach ($modulePermissions as $permission)
                                                <li class="list-group-item {{ in_array($permission->id, $rolePermissions) ? 'list-group-item-success' : 'list-group-item-light' }}">
                                                    @if (in_array($permission->id, $rolePermissions))
                                                        <i class="fas fa-check-circle text-success me-2"></i>
                                                    @else
                                                        <i class="fas fa-times-circle text-muted me-2"></i>
                                                    @endif
                                                    {{ $permission->display_name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

