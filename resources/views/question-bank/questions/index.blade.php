@extends('layouts.question-bank')

@section('title', 'Questions')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Questions</h1>
    <div>
        <a href="{{ route('question-bank.questions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add Question
        </a>
    </div>
</div>

<div class="card shadow mb-4 fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('question-bank.questions.index') }}" method="GET" id="filter-form">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="subject_id" class="form-label">Subject</label>
                    <select class="form-select" id="subject_id" name="subject_id">
                        <option value="">All Subjects</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                {{ $subject->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="unit_id" class="form-label">Unit</label>
                    <select class="form-select" id="unit_id" name="unit_id" {{ request('subject_id') ? '' : 'disabled' }}>
                        <option value="">All Units</option>
                        @if(request('subject_id') && $units)
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                    {{ $unit->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="topic_id" class="form-label">Topic</label>
                    <select class="form-select" id="topic_id" name="topic_id" {{ request('unit_id') ? '' : 'disabled' }}>
                        <option value="">All Topics</option>
                        @if(request('unit_id') && $topics)
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="question_type_id" class="form-label">Question Type</label>
                    <select class="form-select" id="question_type_id" name="question_type_id">
                        <option value="">All Types</option>
                        @foreach($questionTypes as $type)
                            <option value="{{ $type->id }}" {{ request('question_type_id') == $type->id ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="blooms_taxonomy_id" class="form-label">Bloom's Level</label>
                    <select class="form-select" id="blooms_taxonomy_id" name="blooms_taxonomy_id">
                        <option value="">All Levels</option>
                        @foreach($bloomsLevels as $level)
                            <option value="{{ $level->id }}" {{ request('blooms_taxonomy_id') == $level->id ? 'selected' : '' }}>
                                {{ $level->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="difficulty_level" class="form-label">Difficulty</label>
                    <select class="form-select" id="difficulty_level" name="difficulty_level">
                        <option value="">All Difficulties</option>
                        @for($i = 1; $i <= 5; $i++)
                            <option value="{{ $i }}" {{ request('difficulty_level') == $i ? 'selected' : '' }}>
                                {{ $i }} - {{ ['Very Easy', 'Easy', 'Medium', 'Hard', 'Very Hard'][$i-1] }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="marks" class="form-label">Marks</label>
                    <select class="form-select" id="marks" name="marks">
                        <option value="">All Marks</option>
                        @foreach([1, 2, 3, 4, 5, 10, 15, 20] as $mark)
                            <option value="{{ $mark }}" {{ request('marks') == $mark ? 'selected' : '' }}>
                                {{ $mark }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="is_active" class="form-label">Status</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">All Statuses</option>
                        <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search in question text..." value="{{ request('search') }}">
                </div>
                <div class="col-md-6 d-flex align-items-end mb-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('question-bank.questions.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">All Questions</h6>
        <span class="badge bg-primary">{{ $questions->total() }} Questions</span>
    </div>
    <div class="card-body">
        @if($questions->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>Question</th>
                            <th>Bloom's Level</th>
                            <th>Difficulty</th>
                            <th>Marks</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questions as $question)
                            <tr>
                                <td>{{ $question->topic->unit->subject->name }}</td>
                                <td>{{ $question->questionType->name }}</td>
                                <td>
                                    <a href="{{ route('question-bank.questions.show', $question) }}" class="fw-bold text-decoration-none">
                                        {{ Str::limit($question->question_text, 50) }}
                                    </a>
                                </td>
                                <td>{{ $question->bloomsTaxonomy->name ?? 'N/A' }}</td>
                                <td>
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $question->difficulty_level)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    @endfor
                                </td>
                                <td>{{ $question->marks }}</td>
                                <td>
                                    @if($question->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('question-bank.questions.show', $question) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('question-bank.questions.edit', $question) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('question-bank.questions.toggle-active', $question) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $question->is_active ? 'btn-secondary' : 'btn-success' }}" data-bs-toggle="tooltip" title="{{ $question->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas {{ $question->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('question-bank.questions.clone', $question) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Clone">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('question-bank.questions.destroy', $question) }}" method="POST" class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center mt-4">
                {{ $questions->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No questions found matching your criteria. 
                <a href="{{ route('question-bank.questions.create') }}" class="alert-link">Create a new question</a> or 
                <a href="{{ route('question-bank.questions.index') }}" class="alert-link">reset filters</a>.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirm delete
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this question? This action cannot be undone.')) {
            this.submit();
        }
    });
    
    // Dynamic dropdowns for subject -> unit -> topic
    $('#subject_id').on('change', function() {
        const subjectId = $(this).val();
        const unitDropdown = $('#unit_id');
        const topicDropdown = $('#topic_id');
        
        // Reset unit and topic dropdowns
        unitDropdown.html('<option value="">All Units</option>');
        topicDropdown.html('<option value="">All Topics</option>');
        
        if (subjectId) {
            unitDropdown.prop('disabled', false);
            
            // Fetch units for the selected subject
            $.ajax({
                url: `/api/subjects/${subjectId}/units`,
                type: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        data.forEach(function(unit) {
                            unitDropdown.append(`<option value="${unit.id}">${unit.name}</option>`);
                        });
                    }
                }
            });
        } else {
            unitDropdown.prop('disabled', true);
            topicDropdown.prop('disabled', true);
        }
    });
    
    $('#unit_id').on('change', function() {
        const unitId = $(this).val();
        const topicDropdown = $('#topic_id');
        
        // Reset topic dropdown
        topicDropdown.html('<option value="">All Topics</option>');
        
        if (unitId) {
            topicDropdown.prop('disabled', false);
            
            // Fetch topics for the selected unit
            $.ajax({
                url: `/api/units/${unitId}/topics`,
                type: 'GET',
                success: function(data) {
                    if (data.length > 0) {
                        data.forEach(function(topic) {
                            topicDropdown.append(`<option value="${topic.id}">${topic.name}</option>`);
                        });
                    }
                }
            });
        } else {
            topicDropdown.prop('disabled', true);
        }
    });
</script>
@endpush

