@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Bloom's Taxonomy Level</h1>
        <a href="{{ route('blooms-taxonomy.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Bloom's Taxonomy
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('blooms-taxonomy.update', $bloomsTaxonomy->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="level_name" class="form-label">Level Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('level_name') is-invalid @enderror" id="level_name" name="level_name" value="{{ old('level_name', $bloomsTaxonomy->level_name) }}" required>
                            @error('level_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $bloomsTaxonomy->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="example_verbs" class="form-label">Example Verbs</label>
                            <textarea class="form-control @error('example_verbs') is-invalid @enderror" id="example_verbs" name="example_verbs" rows="3" placeholder="Enter comma-separated verbs (e.g., define, list, recall)">{{ old('example_verbs', $bloomsTaxonomy->example_verbs) }}</textarea>
                            <div class="form-text">Enter comma-separated verbs that are associated with this cognitive level.</div>
                            @error('example_verbs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="level_order" class="form-label">Level Order <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('level_order') is-invalid @enderror" id="level_order" name="level_order" value="{{ old('level_order', $bloomsTaxonomy->level_order) }}" min="1" required>
                            <div class="form-text">The hierarchical order of this level (1 being the lowest).</div>
                            @error('level_order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Update Level</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bloom's Taxonomy Guide</h5>
                </div>
                <div class="card-body">
                    <p>Bloom's Taxonomy is a hierarchical ordering of cognitive skills that can help teachers teach and students learn.</p>
                    
                    <h6 class="fw-bold mt-3">Revised Taxonomy (2001)</h6>
                    <ol class="list-group list-group-numbered mb-3">
                        <li class="list-group-item">
                            <strong>Remember</strong>
                            <p class="mb-0 small">Recall facts and basic concepts</p>
                            <p class="mb-0 small text-muted">Verbs: define, list, recall, memorize</p>
                        </li>
                        <li class="list-group-item">
                            <strong>Understand</strong>
                            <p class="mb-0 small">Explain ideas or concepts</p>
                            <p class="mb-0 small text-muted">Verbs: explain, describe, discuss, interpret</p>
                        </li>
                        <li class="list-group-item">
                            <strong>Apply</strong>
                            <p class="mb-0 small">Use information in new situations</p>
                            <p class="mb-0 small text-muted">Verbs: solve, implement, use, demonstrate</p>
                        </li>
                        <li class="list-group-item">
                            <strong>Analyze</strong>
                            <p class="mb-0 small">Draw connections among ideas</p>
                            <p class="mb-0 small text-muted">Verbs: compare, contrast, examine, categorize</p>
                        </li>
                        <li class="list-group-item">
                            <strong>Evaluate</strong>
                            <p class="mb-0 small">Justify a stand or decision</p>
                            <p class="mb-0 small text-muted">Verbs: judge, critique, defend, justify</p>
                        </li>
                        <li class="list-group-item">
                            <strong>Create</strong>
                            <p class="mb-0 small">Produce new or original work</p>
                            <p class="mb-0 small text-muted">Verbs: design, develop, formulate, create</p>
                        </li>
                    </ol>
                    
                    @if($bloomsTaxonomy->questions_count > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> This level has {{ $bloomsTaxonomy->questions_count }} questions associated with it. Changing the level name or order may affect question categorization.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

