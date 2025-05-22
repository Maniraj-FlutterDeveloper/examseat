@extends('layouts.question-bank')

@section('title', 'Edit Unit')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.subjects.index') }}">Subjects</a></li>
        <li class="breadcrumb-item"><a href="{{ route('question-bank.subjects.show', $subject) }}">{{ $subject->name }}</a></li>
        <li class="breadcrumb-item"><a href="{{ route('question-bank.subjects.units.show', [$subject, $unit]) }}">{{ $unit->name }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Edit Unit</h1>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Unit Information</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('question-bank.subjects.units.update', [$subject, $unit]) }}" method="POST">
            @csrf
            @method('PATCH')
            
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $unit->name) }}" required>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="code" class="form-label">Code</label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $unit->code) }}">
                <small class="form-text text-muted">A unique code for the unit (e.g., UNIT01)</small>
                @error('code')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $unit->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="order" class="form-label">Order</label>
                <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', $unit->order) }}" min="1">
                <small class="form-text text-muted">The order in which this unit appears in the subject.</small>
                @error('order')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', $unit->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
                <small class="form-text text-muted d-block">Inactive units will not be available for selection in other modules.</small>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('question-bank.subjects.units.show', [$subject, $unit]) }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

