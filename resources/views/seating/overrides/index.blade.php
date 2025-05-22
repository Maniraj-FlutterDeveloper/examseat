@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Overrides</h5>
                    <a href="{{ route('seating.overrides.create') }}" class="btn btn-primary">Create New Override</a>
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
                                    <th>Seating Plan</th>
                                    <th>Student</th>
                                    <th>Room</th>
                                    <th>Seat Number</th>
                                    <th>Reason</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($overrides as $override)
                                    <tr>
                                        <td>{{ $override->id }}</td>
                                        <td>{{ $override->seatingPlan->exam_name }}</td>
                                        <td>{{ $override->student->name }} ({{ $override->student->roll_number }})</td>
                                        <td>{{ $override->room->room_number }}</td>
                                        <td>{{ $override->seat_number }}</td>
                                        <td>{{ Str::limit($override->reason, 30) }}</td>
                                        <td>{{ $override->created_by }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seating.overrides.show', $override) }}" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('seating.overrides.edit', $override) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <form action="{{ route('seating.overrides.destroy', $override) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this override?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No seating overrides found.</td>
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

