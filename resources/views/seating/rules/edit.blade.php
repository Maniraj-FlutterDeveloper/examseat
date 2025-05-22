@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Edit Seating Rule</h5>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('seating.rules.update', $rule) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="name" class="form-label">Rule Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $rule->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="type" class="form-label">Rule Type</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select Rule Type</option>
                                <option value="alternate_courses" {{ (old('type', $rule->type) == 'alternate_courses') ? 'selected' : '' }}>Alternate Courses</option>
                                <option value="distance" {{ (old('type', $rule->type) == 'distance') ? 'selected' : '' }}>Distance</option>
                                <option value="priority" {{ (old('type', $rule->type) == 'priority') ? 'selected' : '' }}>Priority</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $rule->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="parameters" class="form-label">Parameters (JSON)</label>
                            <textarea class="form-control @error('parameters') is-invalid @enderror" id="parameters" name="parameters" rows="5">{{ old('parameters', json_encode($rule->parameters, JSON_PRETTY_PRINT)) }}</textarea>
                            <div class="form-text">
                                Enter parameters in JSON format. Examples:
                                <ul>
                                    <li>Alternate Courses: <code>{"min_distance": 1}</code></li>
                                    <li>Distance: <code>{"distance": 2}</code></li>
                                    <li>Priority: <code>{"seats_per_row": 5, "door_seat": 1}</code></li>
                                </ul>
                            </div>
                            @error('parameters')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority</label>
                            <input type="number" class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', $rule->priority) }}" min="0">
                            <div class="form-text">Higher number means higher priority.</div>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_active') is-invalid @enderror" id="is_active" name="is_active" {{ old('is_active', $rule->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('seating.rules.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Rule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

