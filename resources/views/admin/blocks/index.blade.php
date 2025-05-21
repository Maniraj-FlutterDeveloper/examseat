@extends('layouts.admin')

@section('title', 'Manage Blocks')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Manage Blocks</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.blocks.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Add New Block
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">All Blocks</h5>
        </div>
        <div class="card-body">
            @if($blocks->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Block Name</th>
                            <th>Description</th>
                            <th>Rooms</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blocks as $block)
                        <tr>
                            <td>{{ $block->id }}</td>
                            <td>{{ $block->block_name }}</td>
                            <td>{{ $block->description ?? 'N/A' }}</td>
                            <td>{{ $block->rooms->count() }}</td>
                            <td>{{ $block->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.blocks.show', $block) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.blocks.edit', $block) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $block->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $block->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $block->id }}" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $block->id }}">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete the block <strong>{{ $block->block_name }}</strong>?
                                                @if($block->rooms->count() > 0)
                                                <div class="alert alert-warning mt-3">
                                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                                    This block has {{ $block->rooms->count() }} room(s) associated with it. Deleting this block will not be possible until all rooms are removed or reassigned.
                                                </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                @if($block->rooms->count() == 0)
                                                <form action="{{ route('admin.blocks.destroy', $block) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                                @else
                                                <button type="button" class="btn btn-danger" disabled>Delete</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $blocks->links() }}
            </div>
            @else
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                No blocks found. Click the "Add New Block" button to create one.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
