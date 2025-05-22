@extends('layouts.app')

@section('title', 'Room Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-door-open mr-2"></i> Room Details: {{ $room->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary">
                                <i class="fas fa-edit mr-1"></i> Edit Room
                            </a>
                            <a href="{{ route('rooms.layout', $room) }}" class="btn btn-warning">
                                <i class="fas fa-th mr-1"></i> Room Layout
                            </a>
                            <a href="{{ route('rooms.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Room Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">ID</th>
                                            <td>{{ $room->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $room->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Code</th>
                                            <td>{{ $room->code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Block</th>
                                            <td>
                                                <a href="{{ route('blocks.show', $room->block) }}">
                                                    {{ $room->block->name }} ({{ $room->block->code }})
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Floor</th>
                                            <td>{{ $room->floor ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Capacity</th>
                                            <td>{{ $room->capacity }} students</td>
                                        </tr>
                                        <tr>
                                            <th>Layout</th>
                                            <td>
                                                @if($room->hasGridLayout())
                                                    {{ $room->rows }} rows × {{ $room->columns }} columns
                                                @else
                                                    No grid layout defined
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Has Projector</th>
                                            <td>
                                                <span class="badge badge-{{ $room->has_projector ? 'success' : 'secondary' }}">
                                                    {{ $room->has_projector ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Has Computer</th>
                                            <td>
                                                <span class="badge badge-{{ $room->has_computer ? 'success' : 'secondary' }}">
                                                    {{ $room->has_computer ? 'Yes' : 'No' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge badge-{{ $room->is_active ? 'success' : 'danger' }}">
                                                    {{ $room->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>{{ $room->description ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $room->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $room->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Room Layout Preview</h6>
                                </div>
                                <div class="card-body text-center">
                                    @if($room->hasGridLayout())
                                        <div class="room-layout-preview">
                                            <div class="mb-3">
                                                <span class="badge badge-primary">Front of Room (Blackboard/Projector)</span>
                                            </div>
                                            <table class="table table-bordered room-grid">
                                                @for($row = 1; $row <= min(10, $room->rows); $row++)
                                                    <tr>
                                                        @for($col = 1; $col <= min(10, $room->columns); $col++)
                                                            <td class="seat">
                                                                <div class="seat-number">{{ (($row-1) * $room->columns) + $col }}</div>
                                                                <i class="fas fa-chair"></i>
                                                            </td>
                                                        @endfor
                                                    </tr>
                                                @endfor
                                            </table>
                                            @if($room->rows > 10 || $room->columns > 10)
                                                <div class="mt-2">
                                                    <span class="text-muted">
                                                        <i class="fas fa-info-circle"></i> 
                                                        This is a preview. The actual layout has {{ $room->rows }} rows and {{ $room->columns }} columns.
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="mt-3">
                                                <a href="{{ route('rooms.layout', $room) }}" class="btn btn-primary">
                                                    <i class="fas fa-th mr-1"></i> View Full Layout
                                                </a>
                                            </div>
                                        </div>
                                    @else
                                        <div class="alert alert-warning">
                                            <i class="fas fa-exclamation-triangle mr-2"></i>
                                            No grid layout has been defined for this room. Please edit the room to add rows and columns.
                                        </div>
                                        <div class="mt-3">
                                            <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary">
                                                <i class="fas fa-edit mr-1"></i> Define Layout
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Room Usage Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $room->seatingAssignments->count() }}</h1>
                                                    <p class="lead">Total Assignments</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $room->invigilatorAssignments->count() }}</h1>
                                                    <p class="lead">Invigilator Duties</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <h6>Recent Exams in this Room</h6>
                                        <ul class="list-group">
                                            @forelse($room->seatingAssignments->unique('seating_plan_id')->take(5) as $assignment)
                                                <li class="list-group-item">
                                                    <a href="{{ route('seating-plans.show', $assignment->seatingPlan) }}">
                                                        {{ $assignment->seatingPlan->title }}
                                                    </a>
                                                    <span class="badge badge-{{ $assignment->seatingPlan->status_color }} float-right">
                                                        {{ ucfirst($assignment->seatingPlan->status) }}
                                                    </span>
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ \Carbon\Carbon::parse($assignment->seatingPlan->exam_date)->format('M d, Y') }}
                                                        at {{ \Carbon\Carbon::parse($assignment->seatingPlan->start_time)->format('h:i A') }}
                                                    </small>
                                                </li>
                                            @empty
                                                <li class="list-group-item text-center">
                                                    No exams have been scheduled in this room yet.
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .room-grid {
        margin: 0 auto;
        max-width: 100%;
        border-collapse: separate;
        border-spacing: 5px;
    }
    
    .seat {
        width: 40px;
        height: 40px;
        text-align: center;
        vertical-align: middle;
        background-color: #f8f9fa;
        position: relative;
        padding: 0;
    }
    
    .seat-number {
        position: absolute;
        top: 2px;
        left: 2px;
        font-size: 10px;
        color: #6c757d;
    }
    
    .seat i {
        font-size: 18px;
        color: #3e92cc;
    }
</style>
@endsection

