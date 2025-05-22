@extends('layouts.app')

@section('title', 'Block Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building mr-2"></i> Block Details: {{ $block->name }}
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
                            <a href="{{ route('blocks.edit', $block) }}" class="btn btn-primary">
                                <i class="fas fa-edit mr-1"></i> Edit Block
                            </a>
                            <a href="{{ route('blocks.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Block Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 30%">ID</th>
                                            <td>{{ $block->id }}</td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $block->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Code</th>
                                            <td>{{ $block->code }}</td>
                                        </tr>
                                        <tr>
                                            <th>Location</th>
                                            <td>{{ $block->location ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Description</th>
                                            <td>{{ $block->description ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status</th>
                                            <td>
                                                <span class="badge badge-{{ $block->is_active ? 'success' : 'danger' }}">
                                                    {{ $block->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Created At</th>
                                            <td>{{ $block->created_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Updated At</th>
                                            <td>{{ $block->updated_at->format('M d, Y h:i A') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Block Statistics</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $block->rooms->count() }}</h1>
                                                    <p class="lead">Total Rooms</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $block->total_capacity }}</h1>
                                                    <p class="lead">Total Capacity</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mt-3">
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $block->active_rooms_count }}</h1>
                                                    <p class="lead">Active Rooms</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card bg-light">
                                                <div class="card-body text-center">
                                                    <h1 class="display-4">{{ $block->rooms->count() - $block->active_rooms_count }}</h1>
                                                    <p class="lead">Inactive Rooms</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-4">
                        <div class="card-header bg-primary text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Rooms in this Block</h6>
                                <a href="{{ route('rooms.create') }}" class="btn btn-sm btn-light">
                                    <i class="fas fa-plus-circle mr-1"></i> Add Room
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="rooms-table">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Capacity</th>
                                            <th>Floor</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($block->rooms as $room)
                                            <tr>
                                                <td>{{ $room->id }}</td>
                                                <td>{{ $room->name }}</td>
                                                <td>{{ $room->code }}</td>
                                                <td>{{ $room->capacity }}</td>
                                                <td>{{ $room->floor ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge badge-{{ $room->is_active ? 'success' : 'danger' }}">
                                                        {{ $room->is_active ? 'Active' : 'Inactive' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('rooms.show', $room) }}" class="btn btn-info btn-sm" title="View">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('rooms.edit', $room) }}" class="btn btn-primary btn-sm" title="Edit">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <a href="{{ route('rooms.layout', $room) }}" class="btn btn-warning btn-sm" title="Layout">
                                                            <i class="fas fa-th"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center">No rooms found in this block.</td>
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
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#rooms-table').DataTable({
            "responsive": true,
            "language": {
                "search": "Search rooms:",
                "lengthMenu": "Show _MENU_ rooms per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ rooms",
                "infoEmpty": "Showing 0 to 0 of 0 rooms",
                "zeroRecords": "No matching rooms found"
            }
        });
    });
</script>
@endsection

