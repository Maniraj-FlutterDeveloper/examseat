@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Add New Bloom's Taxonomy Level</h1>
        <a href="{{ route('admin.blooms-taxonomy.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Bloom's Taxonomy
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.blooms-taxonomy.store') }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="level_name" class="form-label">Level Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('level_name') is-invalid @enderror" id="level_name" name="level_name" value="{{ old('level_name') }}" required>
                            @error('level_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter the name of the cognitive level (e.g., Remember, Understand, Apply)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Provide a detailed description of this cognitive level</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="order" class="form-label">Order <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $nextOrder) }}" min="1" required>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">The display order of this level (lower numbers appear first)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="example_verbs" class="form-label">Example Verbs</label>
                            <input type="text" class="form-control @error('example_verbs') is-invalid @enderror" id="example_verbs" name="example_verbs" value="{{ old('example_verbs') }}">
                            @error('example_verbs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Comma-separated list of verbs associated with this level (e.g., Define, List, Recall)</small>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="reset" class="btn btn-light me-md-2">Reset</button>
                            <button type="submit" class="btn btn-primary">Save Level</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Bloom's Taxonomy Guide</h5>
                </div>
                <div class="card-body">
                    <h6>Traditional Levels (1956):</h6>
                    <ol>
                        <li><strong>Knowledge:</strong> Recall data or information</li>
                        <li><strong>Comprehension:</strong> Understand the meaning</li>
                        <li><strong>Application:</strong> Use a concept in a new situation</li>
                        <li><strong>Analysis:</strong> Separate concepts into parts</li>
                        <li><strong>Synthesis:</strong> Build a structure from diverse elements</li>
                        <li><strong>Evaluation:</strong> Make judgments about the value</li>
                    </ol>
                    
                    <h6 class="mt-4">Revised Levels (2001):</h6>
                    <ol>
                        <li><strong>Remember:</strong> Recall facts and basic concepts</li>
                        <li><strong>Understand:</strong> Explain ideas or concepts</li>
                        <li><strong>Apply:</strong> Use information in new situations</li>
                        <li><strong>Analyze:</strong> Draw connections among ideas</li>
                        <li><strong>Evaluate:</strong> Justify a stand or decision</li>
                        <li><strong>Create:</strong> Produce new or original work</li>
                    </ol>
                    
                    <div class="alert alert-info mt-4">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Tip:</strong> You can customize these levels or add your own based on your institution's requirements.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

