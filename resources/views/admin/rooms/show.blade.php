@extends('layouts.admin')

@section('title', 'Room Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Room Details</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.rooms.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Rooms
            </a>
            <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Edit Room
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Room Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">Block:</th>
                            <td>
                                <a href="{{ route('admin.blocks.show', $room->block) }}">
                                    {{ $room->block->block_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Room Number:</th>
                            <td>{{ $room->room_number }}</td>
                        </tr>
                        <tr>
                            <th>Capacity:</th>
                            <td>{{ $room->capacity }} seats</td>
                        </tr>
                        <tr>
                            <th>Dimensions:</th>
                            <td>{{ $room->rows }} × {{ $room->columns }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $room->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($room->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $room->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $room->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Room Layout</h5>
                </div>
                <div class="card-body">
                    <div class="room-layout">
                        <div class="text-center mb-3">
                            <div class="badge bg-primary p-2 mb-3">FRONT (Whiteboard/Projector)</div>
                            
                            <div class="table-responsive">
                                <table class="table table-bordered room-layout-table">
                                    <tbody>
                                        @for($row = 1; $row <= $room->rows; $row++)
                                            <tr>
                                                @for($col = 1; $col <= $room->columns; $col++)
                                                    @php
                                                        $seatNumber = (($row - 1) * $room->columns) + $col;
                                                    @endphp
                                                    <td class="text-center p-2">
                                                        <div class="seat-number">{{ $seatNumber }}</div>
                                                    </td>
                                                @endfor
                                            </tr>
                                        @endfor
                                    </tbody>
                                </table>
                            </div>
                            
                            <div class="badge bg-secondary p-2 mt-3">BACK (Entrance)</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            This is the default layout. Actual seating arrangements may vary based on the seating plan.
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Seating Plans Using This Room</h5>
                    <a href="{{ route('admin.seating-plans.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> Create Seating Plan
                    </a>
                </div>
                <div class="card-body">
                    @if($room->seatingPlans->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Exam Name</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($room->seatingPlans->unique('exam_name') as $plan)
                                <tr>
                                    <td>{{ $plan->exam_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->exam_date)->format('M d, Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($plan->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($plan->end_time)->format('h:i A') }}</td>
                                    <td>{{ $room->seatingPlans->where('exam_name', $plan->exam_name)->count() }} / {{ $room->capacity }}</td>
                                    <td>
                                        <span class="badge bg-{{ $plan->status === 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($plan->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.seating-plans.show', ['seating_plan' => $plan->id]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No seating plans have been created for this room yet.
                        <a href="{{ route('admin.seating-plans.create') }}" class="alert-link">Create a seating plan now</a>.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('styles')
<style>
    .room-layout-table {
        width: auto;
        margin: 0 auto;
    }
    
    .room-layout-table td {
        width: 60px;
        height: 60px;
        text-align: center;
        vertical-align: middle;
        background-color: #f8f9fa;
    }
    
    .seat-number {
        font-weight: bold;
    }
</style>
@endsection
@endsection
