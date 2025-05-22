@extends('layouts.app')

@section('title', 'Rooms Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-door-open mr-2"></i> Rooms Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-right">
                        <a href="{{ route('rooms.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle mr-1"></i> Add New Room
                        </a>
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
                        <table class="table table-bordered table-striped" id="rooms-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Block</th>
                                    <th>Capacity</th>
                                    <th>Layout</th>
                                    <th>Floor</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rooms as $room)
                                    <tr>
                                        <td>{{ $room->id }}</td>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ $room->code }}</td>
                                        <td>{{ $room->block->name }}</td>
                                        <td>{{ $room->capacity }}</td>
                                        <td>
                                            @if($room->hasGridLayout())
                                                {{ $room->rows }} × {{ $room->columns }}
                                            @else
                                                No Grid
                                            @endif
                                        </td>
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
                                                <form action="{{ route('rooms.destroy', $room) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this room?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('rooms.toggle-active', $room) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-{{ $room->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $room->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $room->is_active ? 'times' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No rooms found.</td>
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

@section('scripts')
<script>
    $(document).ready(function() {
        $('#rooms-table').DataTable({
            "order": [[ 0, "desc" ]],
            "responsive": true,
            "language": {
                "search": "Search rooms:",
                "lengthMenu": "Show _MENU_ rooms per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ rooms",
                "infoEmpty": "Showing 0 to 0 of 0 rooms",
                "zeroRecords": "No matching rooms found",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            }
        });
    });
</script>
@endsection

