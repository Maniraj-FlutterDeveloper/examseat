@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Block Details</h1>
        <div>
            <a href="{{ route('blocks.edit', $block->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('blocks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Blocks
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
                            <th style="width: 30%">ID:</th>
                            <td>{{ $block->id }}</td>
                        </tr>
                        <tr>
                            <th>Block Name:</th>
                            <td>{{ $block->block_name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $block->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $block->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $block->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Rooms in this Block</h5>
                    <a href="{{ route('rooms.create') }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Room
                    </a>
                </div>
                <div class="card-body">
                    @if($block->rooms->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Room Number</th>
                                        <th>Capacity</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($block->rooms as $room)
                                        <tr>
                                            <td>{{ $room->room_number }}</td>
                                            <td>{{ $room->capacity }}</td>
                                            <td>
                                                @if($room->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('rooms.show', $room->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('rooms.edit', $room->id) }}" class="btn btn-sm btn-warning">
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
                        <div class="text-center py-4">
                            <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                            <p>No rooms have been added to this block yet.</p>
                            <a href="{{ route('rooms.create') }}" class="btn btn-primary">Add Room</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

