@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Add New Block</h1>
        <a href="{{ route('blocks.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Blocks
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('blocks.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="block_name" class="form-label">Block Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('block_name') is-invalid @enderror" id="block_name" name="block_name" value="{{ old('block_name') }}" required>
                    @error('block_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Create Block</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

