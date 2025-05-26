@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Plans</h5>
                    <a href="{{ route('seating.plans.create') }}" class="btn btn-primary">Create New Plan</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger" role="alert">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Exam Name</th>
                                    <th>Room</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($seatingPlans as $plan)
                                    <tr>
                                        <td>{{ $plan->id }}</td>
                                        <td>{{ $plan->exam_name }}</td>
                                        <td>{{ $plan->room->room_number }}</td>
                                        <td>{{ $plan->exam_date->format('M d, Y') }}</td>
                                        <td>{{ $plan->start_time->format('H:i') }} - {{ $plan->end_time->format('H:i') }}</td>
                                        <td>
                                            <span class="badge bg-{{ $plan->status == 'scheduled' ? 'primary' : ($plan->status == 'ongoing' ? 'warning' : ($plan->status == 'completed' ? 'success' : 'danger')) }}">
                                                {{ ucfirst($plan->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seating.plans.show', $plan) }}" class="btn btn-sm btn-info">View</a>
                                                <a href="{{ route('seating.plans.edit', $plan) }}" class="btn btn-sm btn-warning">Edit</a>
                                                <a href="{{ route('seating.plans.generate', $plan) }}" class="btn btn-sm btn-primary">Generate</a>
                                                <form action="{{ route('seating.plans.destroy', $plan) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this seating plan?')">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No seating plans found.</td>
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

