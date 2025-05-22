@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Rules</h5>
                    <a href="{{ route('seating.rules.create') }}" class="btn btn-primary">Create New Rule</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($rules as $rule)
                                    <tr>
                                        <td>{{ $rule->id }}</td>
                                        <td>{{ $rule->name }}</td>
                                        <td>
                                            <span class="badge bg-{{ $rule->type == 'alternate_courses' ? 'primary' : ($rule->type == 'distance' ? 'info' : 'warning') }}">
                                                {{ str_replace('_', ' ', ucfirst($rule->type)) }}
                                            </span>
                                        </td>
                                        <td>{{ $rule->priority }}</td>
                                        <td>
                                            <span class="badge bg-{{ $rule->is_active ? 'success' : 'danger' }}">
                                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seating.rules.show', $rule) }}" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('seating.rules.edit', $rule) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('seating.rules.destroy', $rule) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this rule?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No seating rules found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

