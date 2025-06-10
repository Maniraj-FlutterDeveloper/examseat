@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Edit Bloom's Taxonomy Level</h1>
        <a href="{{ route('admin.blooms-taxonomy.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Bloom's Taxonomy
        </a>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('admin.blooms-taxonomy.update', $bloomsTaxonomy->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="level_name" class="form-label">Level Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('level_name') is-invalid @enderror" id="level_name" name="level_name" value="{{ old('level_name', $bloomsTaxonomy->level_name) }}" required>
                            @error('level_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Enter the name of the cognitive level (e.g., Remember, Understand, Apply)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" required>{{ old('description', $bloomsTaxonomy->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Provide a detailed description of this cognitive level</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="order" class="form-label">Order <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $bloomsTaxonomy->order) }}" min="1" required>
                            @error('order')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">The display order of this level (lower numbers appear first)</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="example_verbs" class="form-label">Example Verbs</label>
                            <input type="text" class="form-control @error('example_verbs') is-invalid @enderror" id="example_verbs" name="example_verbs" value="{{ old('example_verbs', $bloomsTaxonomy->example_verbs) }}">
                            @error('example_verbs')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Comma-separated list of verbs associated with this level (e.g., Define, List, Recall)</small>
                        </div>
                        
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('admin.blooms-taxonomy.index') }}" class="btn btn-light me-md-2">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Level</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Level Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>ID</th>
                            <td>{{ $bloomsTaxonomy->id }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $bloomsTaxonomy->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $bloomsTaxonomy->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Questions</th>
                            <td>{{ $bloomsTaxonomy->questions()->count() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Usage Guidelines</h5>
                </div>
                <div class="card-body">
                    <p>When editing a Bloom's Taxonomy level:</p>
                    <ul>
                        <li>Ensure the level name clearly represents the cognitive skill</li>
                        <li>Provide a comprehensive description that helps question creators understand the level</li>
                        <li>Include relevant example verbs to guide question formulation</li>
                        <li>Consider the hierarchical order in relation to other levels</li>
                    </ul>
                    
                    <div class="alert alert-warning mt-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Note:</strong> Changing a level will affect all questions associated with it.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

