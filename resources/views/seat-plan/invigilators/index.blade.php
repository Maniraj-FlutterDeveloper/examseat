@extends('layouts.app')

@section('title', 'Invigilators Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-tie mr-2"></i> Invigilators Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ route('invigilators.index') }}" method="GET" class="form-inline">
                                    <div class="form-group mr-2">
                                        <select name="department" class="form-control select2">
                                            <option value="">All Departments</option>
                                            @foreach($departments as $dept)
                                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>
                                                    {{ $dept }}
                                                </option>
                                            @endforeach
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
                                    <a href="{{ route('invigilators.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset
                                    </a>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('invigilators.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle mr-1"></i> Add New Invigilator
                                </a>
                                <a href="{{ route('invigilators.import') }}" class="btn btn-warning ml-2">
                                    <i class="fas fa-file-import mr-1"></i> Import
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
                        <table class="table table-bordered table-striped" id="invigilators-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Employee ID</th>
                                    <th>Department</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Assignments</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invigilators as $invigilator)
                                    <tr>
                                        <td>{{ $invigilator->id }}</td>
                                        <td>{{ $invigilator->name }}</td>
                                        <td>{{ $invigilator->employee_id }}</td>
                                        <td>{{ $invigilator->department ?? 'N/A' }}</td>
                                        <td>{{ $invigilator->email ?? 'N/A' }}</td>
                                        <td>{{ $invigilator->phone ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge badge-info">
                                                {{ $invigilator->assignments_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $invigilator->is_active ? 'success' : 'danger' }}">
                                                {{ $invigilator->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('invigilators.show', $invigilator) }}" class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('invigilators.edit', $invigilator) }}" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('invigilators.destroy', $invigilator) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this invigilator?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('invigilators.toggle-active', $invigilator) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-{{ $invigilator->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $invigilator->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $invigilator->is_active ? 'times' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No invigilators found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $invigilators->appends(request()->except('page'))->links() }}
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
        $('.select2').select2({
            placeholder: 'Select a department',
            allowClear: true
        });
        
        $('#invigilators-table').DataTable({
            "paging": false,
            "info": false,
            "responsive": true,
            "language": {
                "search": "Search invigilators:",
                "zeroRecords": "No matching invigilators found"
            }
        });
    });
</script>
@endsection

