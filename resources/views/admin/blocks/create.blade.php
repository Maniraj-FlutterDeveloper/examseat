@extends('layouts.admin')

@section('title', 'Add New Block')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Add New Block</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('admin.blocks.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i> Back to Blocks
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Block Details</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.blocks.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="block_name" class="form-label">Block Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('block_name') is-invalid @enderror" id="block_name" name="block_name" value="{{ old('block_name') }}" required>
                    @error('block_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Enter a unique name for the block (e.g., "Block A", "Science Block")</div>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="form-text">Provide a brief description of the block (optional)</div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-outline-secondary">Reset</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Save Block
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
