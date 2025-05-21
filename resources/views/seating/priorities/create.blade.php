@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Create Student Priority</h5>
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

                    <form method="POST" action="{{ route('seating.priorities.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="student_id" class="form-label">Student</label>
                            <select class="form-select @error('student_id') is-invalid @enderror" id="student_id" name="student_id" required>
                                <option value="">Select Student</option>
                                @foreach ($students as $student)
                                    <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                        {{ $student->name }} ({{ $student->roll_number }})
                                    </option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority_type" class="form-label">Priority Type</label>
                            <select class="form-select @error('priority_type') is-invalid @enderror" id="priority_type" name="priority_type" required>
                                <option value="">Select Priority Type</option>
                                <option value="disability" {{ old('priority_type') == 'disability' ? 'selected' : '' }}>Disability</option>
                                <option value="medical" {{ old('priority_type') == 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="other" {{ old('priority_type') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                            @error('priority_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority_level" class="form-label">Priority Level</label>
                            <input type="number" class="form-control @error('priority_level') is-invalid @enderror" id="priority_level" name="priority_level" value="{{ old('priority_level', 1) }}" min="1" max="10" required>
                            <div class="form-text">1 (lowest) to 10 (highest)</div>
                            @error('priority_level')
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

                        <div class="mb-3">
                            <label for="requirements" class="form-label">Special Requirements</label>
                            <textarea class="form-control @error('requirements') is-invalid @enderror" id="requirements" name="requirements" rows="3">{{ old('requirements') }}</textarea>
                            @error('requirements')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="valid_until" class="form-label">Valid Until</label>
                            <input type="date" class="form-control @error('valid_until') is-invalid @enderror" id="valid_until" name="valid_until" value="{{ old('valid_until') }}">
                            <div class="form-text">Leave blank for permanent priority</div>
                            @error('valid_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input @error('is_verified') is-invalid @enderror" id="is_verified" name="is_verified" {{ old('is_verified') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_verified">Verified</label>
                            @error('is_verified')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('seating.priorities.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Create Priority</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

