@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Rule Details</h5>
                    <a href="{{ route('seating.rules.edit', $rule) }}" class="btn btn-warning">Edit</a>
                </div>

                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">Name</th>
                            <td>{{ $rule->name }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>
                                <span class="badge bg-{{ $rule->type == 'alternate_courses' ? 'primary' : ($rule->type == 'distance' ? 'info' : 'warning') }}">
                                    {{ str_replace('_', ' ', ucfirst($rule->type)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $rule->description ?? 'No description provided' }}</td>
                        </tr>
                        <tr>
                            <th>Parameters</th>
                            <td>
                                @if($rule->parameters)
                                    <pre class="mb-0"><code>{{ json_encode($rule->parameters, JSON_PRETTY_PRINT) }}</code></pre>
                                @else
                                    <em>No parameters defined</em>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Priority</th>
                            <td>{{ $rule->priority }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                <span class="badge bg-{{ $rule->is_active ? 'success' : 'danger' }}">
                                    {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $rule->created_at->format('F d, Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $rule->updated_at->format('F d, Y h:i A') }}</td>
                        </tr>
                    </table>

                    <div class="mt-4">
                        <a href="{{ route('seating.rules.index') }}" class="btn btn-secondary">Back to List</a>
                        <form action="{{ route('seating.rules.destroy', $rule) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this rule?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

