@extends('layouts.app')

@section('title', 'Seating Rules')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel mr-2"></i> Seating Rules
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ route('seating-rules.index') }}" method="GET" class="form-inline">
                                    <div class="form-group mr-2">
                                        <input type="text" name="search" class="form-control" placeholder="Search by name or description" value="{{ request('search') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="type" class="form-control">
                                            <option value="">All Types</option>
                                            <option value="course" {{ request('type') == 'course' ? 'selected' : '' }}>Course</option>
                                            <option value="room" {{ request('type') == 'room' ? 'selected' : '' }}>Room</option>
                                            <option value="student" {{ request('type') == 'student' ? 'selected' : '' }}>Student</option>
                                            <option value="invigilator" {{ request('type') == 'invigilator' ? 'selected' : '' }}>Invigilator</option>
                                            <option value="general" {{ request('type') == 'general' ? 'selected' : '' }}>General</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Active</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Inactive</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-filter mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('seating-rules.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset
                                    </a>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('seating-rules.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle mr-1"></i> Create New Rule
                                </a>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="seating-rules-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seatingRules as $rule)
                                    <tr>
                                        <td>{{ $rule->id }}</td>
                                        <td>
                                            <a href="{{ route('seating-rules.show', $rule) }}">
                                                {{ $rule->name }}
                                            </a>
                                        </td>
                                        <td>
                                            @if($rule->type == 'course')
                                                <span class="badge badge-primary">Course</span>
                                            @elseif($rule->type == 'room')
                                                <span class="badge badge-info">Room</span>
                                            @elseif($rule->type == 'student')
                                                <span class="badge badge-success">Student</span>
                                            @elseif($rule->type == 'invigilator')
                                                <span class="badge badge-warning">Invigilator</span>
                                            @elseif($rule->type == 'general')
                                                <span class="badge badge-secondary">General</span>
                                            @endif
                                        </td>
                                        <td>{{ Str::limit($rule->description, 50) }}</td>
                                        <td>
                                            <span class="badge badge-{{ $rule->priority > 7 ? 'danger' : ($rule->priority > 4 ? 'warning' : 'info') }}">
                                                {{ $rule->priority }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $rule->is_active ? 'success' : 'danger' }}">
                                                {{ $rule->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>{{ $rule->created_by_name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seating-rules.show', $rule) }}" class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('seating-rules.edit', $rule) }}" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('seating-rules.toggle-active', $rule) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-{{ $rule->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $rule->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $rule->is_active ? 'times' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('seating-rules.destroy', $rule) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this rule?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No seating rules found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $seatingRules->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#seating-rules-table').DataTable({
            "paging": false,
            "info": false,
            "responsive": true,
            "language": {
                "search": "Quick search:",
                "zeroRecords": "No matching rules found"
            }
        });
    });
</script>
@endsection

