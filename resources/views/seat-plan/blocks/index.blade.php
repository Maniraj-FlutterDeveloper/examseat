@extends('layouts.app')

@section('title', 'Blocks Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-building mr-2"></i> Blocks Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 text-right">
                        <a href="{{ route('blocks.create') }}" class="btn btn-success">
                            <i class="fas fa-plus-circle mr-1"></i> Add New Block
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
                        <table class="table table-bordered table-striped" id="blocks-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Location</th>
                                    <th>Rooms</th>
                                    <th>Total Capacity</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($blocks as $block)
                                    <tr>
                                        <td>{{ $block->id }}</td>
                                        <td>{{ $block->name }}</td>
                                        <td>{{ $block->code }}</td>
                                        <td>{{ $block->location ?? 'N/A' }}</td>
                                        <td>{{ $block->rooms->count() }}</td>
                                        <td>{{ $block->total_capacity }}</td>
                                        <td>
                                            <span class="badge badge-{{ $block->is_active ? 'success' : 'danger' }}">
                                                {{ $block->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('blocks.show', $block) }}" class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('blocks.edit', $block) }}" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('blocks.destroy', $block) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this block?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('blocks.toggle-active', $block) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-{{ $block->is_active ? 'warning' : 'success' }} btn-sm" title="{{ $block->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas fa-{{ $block->is_active ? 'times' : 'check' }}"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">No blocks found.</td>
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
        $('#blocks-table').DataTable({
            "order": [[ 0, "desc" ]],
            "responsive": true,
            "language": {
                "search": "Search blocks:",
                "lengthMenu": "Show _MENU_ blocks per page",
                "info": "Showing _START_ to _END_ of _TOTAL_ blocks",
                "infoEmpty": "Showing 0 to 0 of 0 blocks",
                "zeroRecords": "No matching blocks found",
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

