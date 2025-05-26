@extends('layouts.question-bank')

@section('title', 'Edit Bloom\'s Taxonomy Level')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.blooms-taxonomy.index') }}">Bloom's Taxonomy</a></li>
        <li class="breadcrumb-item"><a href="{{ route('question-bank.blooms-taxonomy.show', $bloomsLevel) }}">{{ $bloomsLevel->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Bloom's Taxonomy Level</h1>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Level Information</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('question-bank.blooms-taxonomy.update', $bloomsLevel) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $bloomsLevel->name) }}" required>
                <small class="form-text text-muted">E.g., Remember, Understand, Apply, Analyze, Evaluate, Create</small>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $bloomsLevel->description) }}</textarea>
                <small class="form-text text-muted">A brief description of this cognitive level.</small>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('level') is-invalid @enderror" id="level" name="level" value="{{ old('level', $bloomsLevel->level) }}" min="1" required>
                <small class="form-text text-muted">The hierarchical level (1 being the lowest). This determines the order in which levels are displayed.</small>
                @error('level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $bloomsLevel->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
                <small class="form-text text-muted d-block">Inactive levels will not be available for selection in other modules.</small>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('question-bank.blooms-taxonomy.show', $bloomsLevel) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Level
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

