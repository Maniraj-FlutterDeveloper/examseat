@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Add New Unit</h1>
        <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Units
        </a>
    </div>
    
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.units.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                    <select class="form-select @error('subject_id') is-invalid @enderror" id="subject_id" name="subject_id" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ old('subject_id', request('subject_id')) == $subject->id ? 'selected' : '' }}>
                                {{ $subject->subject_name }} ({{ $subject->subject_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="unit_name" class="form-label">Unit Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('unit_name') is-invalid @enderror" id="unit_name" name="unit_name" value="{{ old('unit_name') }}" required>
                    @error('unit_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="unit_code" class="form-label">Unit Code</label>
                    <input type="text" class="form-control @error('unit_code') is-invalid @enderror" id="unit_code" name="unit_code" value="{{ old('unit_code') }}">
                    @error('unit_code')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Enter a code for this unit (e.g., UNIT1, ALGEBRA)</small>
                </div>
                
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">Order</label>
                    <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}" min="0">
                    @error('order')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Units will be displayed in ascending order (0, 1, 2, ...)</small>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active') ? 'checked' : 'checked' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>
                
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-light me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary">Save Unit</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

