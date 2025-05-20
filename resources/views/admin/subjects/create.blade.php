@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Add New Subject</h1>
        <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Subjects
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.subjects.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="subject_name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('subject_name') is-invalid @enderror" id="subject_name" name="subject_name" value="{{ old('subject_name') }}" required>
                    @error('subject_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="subject_code" class="form-label">Subject Code <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('subject_code') is-invalid @enderror" id="subject_code" name="subject_code" value="{{ old('subject_code') }}" required>
                    @error('subject_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Enter a unique code for this subject (e.g., MATH101, CS201)</small>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-light me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

