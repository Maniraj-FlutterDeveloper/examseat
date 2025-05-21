@extends('layouts.admin')

@section('title', 'Block Details')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Block Details</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.blocks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Blocks
            </a>
            <a href="{{ route('admin.blocks.edit', $block) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i> Edit Block
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Block Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">Block Name:</th>
                            <td>{{ $block->block_name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $block->description ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Total Rooms:</th>
                            <td>{{ $block->rooms->count() }}</td>
                        </tr>
                        <tr>
                            <th>Total Capacity:</th>
                            <td>{{ $block->rooms->sum('capacity') }} seats</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $block->created_at->format('M d, Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $block->updated_at->format('M d, Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rooms in this Block</h5>
                    <a href="{{ route('admin.rooms.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-2"></i> Add Room
                    </a>
                </div>
                <div class="card-body">
                    @if($block->rooms->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Room Number</th>
                                    <th>Capacity</th>
                                    <th>Dimensions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($block->rooms as $room)
                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $room->capacity }} seats</td>
                                    <td>{{ $room->rows }} × {{ $room->columns }}</td>
                                    <td>
                                        @if($room->is_active)
                                        <span class="badge bg-success">Active</span>
                                        @else
                                        <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.rooms.show', $room) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.rooms.edit', $room) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        No rooms have been added to this block yet.
                        <a href="{{ route('admin.rooms.create') }}" class="alert-link">Add a room now</a>.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
