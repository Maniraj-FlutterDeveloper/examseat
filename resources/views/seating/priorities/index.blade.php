@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Student Priorities</h5>
                    <a href="{{ route('seating.priorities.create') }}" class="btn btn-primary">Create New Priority</a>
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
                                    <th>Student</th>
                                    <th>Roll Number</th>
                                    <th>Priority Type</th>
                                    <th>Level</th>
                                    <th>Valid Until</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($priorities as $priority)
                                    <tr>
                                        <td>{{ $priority->id }}</td>
                                        <td>{{ $priority->student->name }}</td>
                                        <td>{{ $priority->student->roll_number }}</td>
                                        <td>
                                            <span class="badge bg-{{ $priority->priority_type == 'disability' ? 'danger' : ($priority->priority_type == 'medical' ? 'warning' : 'info') }}">
                                                {{ ucfirst($priority->priority_type) }}
                                            </span>
                                        </td>
                                        <td>{{ $priority->priority_level }}</td>
                                        <td>
                                            @if($priority->valid_until)
                                                {{ $priority->valid_until->format('M d, Y') }}
                                                @if($priority->valid_until->isPast())
                                                    <span class="badge bg-danger">Expired</span>
                                                @endif
                                            @else
                                                <span class="badge bg-success">Permanent</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seating.priorities.show', $priority) }}" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('seating.priorities.edit', $priority) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('seating.priorities.destroy', $priority) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this priority?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No student priorities found.</td>
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

