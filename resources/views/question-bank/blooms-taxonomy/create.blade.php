@extends('layouts.question-bank')

@section('title', 'Create Bloom\'s Taxonomy Level')

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.blooms-taxonomy.index') }}">Bloom's Taxonomy</a></li>
        <li class="breadcrumb-item active" aria-current="page">Create Level</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Create Bloom's Taxonomy Level</h1>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Level Information</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('question-bank.blooms-taxonomy.store') }}" method="POST">
            @csrf
            
            <div class="mb-3">
                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                <small class="form-text text-muted">E.g., Remember, Understand, Apply, Analyze, Evaluate, Create</small>
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                <small class="form-text text-muted">A brief description of this cognitive level.</small>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3">
                <label for="level" class="form-label">Level <span class="text-danger">*</span></label>
                <input type="number" class="form-control @error('level') is-invalid @enderror" id="level" name="level" value="{{ old('level', $maxLevel + 1) }}" min="1" required>
                <small class="form-text text-muted">The hierarchical level (1 being the lowest). This determines the order in which levels are displayed.</small>
                @error('level')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active</label>
                <small class="form-text text-muted d-block">Inactive levels will not be available for selection in other modules.</small>
            </div>
            
            <div class="d-flex justify-content-end">
                <a href="{{ route('question-bank.blooms-taxonomy.index') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Level
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mt-4 fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Bloom's Taxonomy Reference</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Level</th>
                        <th>Description</th>
                        <th>Example Verbs</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1. Remember</td>
                        <td>Retrieving relevant knowledge from long-term memory.</td>
                        <td>define, duplicate, list, memorize, recall, repeat, reproduce, state</td>
                    </tr>
                    <tr>
                        <td>2. Understand</td>
                        <td>Determining the meaning of instructional messages.</td>
                        <td>classify, describe, discuss, explain, identify, locate, recognize, report, select, translate, paraphrase</td>
                    </tr>
                    <tr>
                        <td>3. Apply</td>
                        <td>Carrying out or using a procedure in a given situation.</td>
                        <td>choose, demonstrate, dramatize, employ, illustrate, interpret, operate, schedule, sketch, solve, use, write</td>
                    </tr>
                    <tr>
                        <td>4. Analyze</td>
                        <td>Breaking material into its constituent parts and detecting how the parts relate to one another and to an overall structure or purpose.</td>
                        <td>appraise, compare, contrast, criticize, differentiate, discriminate, distinguish, examine, experiment, question, test</td>
                    </tr>
                    <tr>
                        <td>5. Evaluate</td>
                        <td>Making judgments based on criteria and standards.</td>
                        <td>appraise, argue, defend, judge, select, support, value, evaluate</td>
                    </tr>
                    <tr>
                        <td>6. Create</td>
                        <td>Putting elements together to form a novel, coherent whole or make an original product.</td>
                        <td>assemble, construct, create, design, develop, formulate, write</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

