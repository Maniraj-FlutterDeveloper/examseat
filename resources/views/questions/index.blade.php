@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Questions</h1>
        <a href="{{ route('questions.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Add New Question
        </a>
    </div>
    
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Questions</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('questions.index') }}" method="GET">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="subject_id" class="form-label">Subject</label>
                        <select class="form-select" id="subject_id" name="subject_id">
                            <option value="">All Subjects</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->subject_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="unit_id" class="form-label">Unit</label>
                        <select class="form-select" id="unit_id" name="unit_id">
                            <option value="">All Units</option>
                            @if(request('subject_id'))
                                @foreach($units->where('subject_id', request('subject_id')) as $unit)
                                    <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>
                                        {{ $unit->unit_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="topic_id" class="form-label">Topic</label>
                        <select class="form-select" id="topic_id" name="topic_id">
                            <option value="">All Topics</option>
                            @if(request('unit_id'))
                                @foreach($topics->where('unit_id', request('unit_id')) as $topic)
                                    <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                        {{ $topic->topic_name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="blooms_taxonomy_id" class="form-label">Bloom's Level</label>
                        <select class="form-select" id="blooms_taxonomy_id" name="blooms_taxonomy_id">
                            <option value="">All Levels</option>
                            @foreach($bloomsTaxonomies as $taxonomy)
                                <option value="{{ $taxonomy->id }}" {{ request('blooms_taxonomy_id') == $taxonomy->id ? 'selected' : '' }}>
                                    {{ $taxonomy->level_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="question_type" class="form-label">Question Type</label>
                        <select class="form-select" id="question_type" name="question_type">
                            <option value="">All Types</option>
                            <option value="mcq" {{ request('question_type') == 'mcq' ? 'selected' : '' }}>Multiple Choice</option>
                            <option value="true_false" {{ request('question_type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                            <option value="short_answer" {{ request('question_type') == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                            <option value="long_answer" {{ request('question_type') == 'long_answer' ? 'selected' : '' }}>Long Answer</option>
                            <option value="fill_in_the_blank" {{ request('question_type') == 'fill_in_the_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                            <option value="matching" {{ request('question_type') == 'matching' ? 'selected' : '' }}>Matching</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="difficulty_level" class="form-label">Difficulty Level</label>
                        <select class="form-select" id="difficulty_level" name="difficulty_level">
                            <option value="">All Difficulties</option>
                            <option value="easy" {{ request('difficulty_level') == 'easy' ? 'selected' : '' }}>Easy</option>
                            <option value="medium" {{ request('difficulty_level') == 'medium' ? 'selected' : '' }}>Medium</option>
                            <option value="hard" {{ request('difficulty_level') == 'hard' ? 'selected' : '' }}>Hard</option>
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="marks" class="form-label">Marks</label>
                        <select class="form-select" id="marks" name="marks">
                            <option value="">All Marks</option>
                            @foreach([1, 2, 3, 4, 5, 10] as $mark)
                                <option value="{{ $mark }}" {{ request('marks') == $mark ? 'selected' : '' }}>{{ $mark }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Search in question text">
                    </div>
                </div>
                
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i>Filter
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Marks</th>
                            <th>Difficulty</th>
                            <th>Bloom's Level</th>
                            <th>Topic</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questions as $question)
                            <tr>
                                <td>{{ $question->id }}</td>
                                <td>{{ Str::limit($question->question_text, 50) }}</td>
                                <td>
                                    @if($question->question_type == 'mcq')
                                        <span class="badge bg-primary">Multiple Choice</span>
                                    @elseif($question->question_type == 'true_false')
                                        <span class="badge bg-info">True/False</span>
                                    @elseif($question->question_type == 'short_answer')
                                        <span class="badge bg-success">Short Answer</span>
                                    @elseif($question->question_type == 'long_answer')
                                        <span class="badge bg-warning">Long Answer</span>
                                    @elseif($question->question_type == 'fill_in_the_blank')
                                        <span class="badge bg-secondary">Fill in the Blank</span>
                                    @elseif($question->question_type == 'matching')
                                        <span class="badge bg-dark">Matching</span>
                                    @endif
                                </td>
                                <td>{{ $question->marks }}</td>
                                <td>
                                    @if($question->difficulty_level == 'easy')
                                        <span class="badge bg-success">Easy</span>
                                    @elseif($question->difficulty_level == 'medium')
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-danger">Hard</span>
                                    @endif
                                </td>
                                <td>{{ $question->bloomsTaxonomy->level_name }}</td>
                                <td>
                                    <a href="{{ route('topics.show', $question->topic_id) }}">
                                        {{ $question->topic->topic_name }}
                                    </a>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('questions.show', $question->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $question->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $question->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $question->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $question->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete this question?
                                                    <div class="alert alert-info mt-3">
                                                        <strong>Question:</strong> {{ Str::limit($question->question_text, 100) }}
                                                    </div>
                                                    @if($question->questionPapers->count() > 0)
                                                        <div class="alert alert-warning mt-3">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            This question is used in {{ $question->questionPapers->count() }} question papers. Deleting this question will remove it from all question papers.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('questions.destroy', $question->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-question-circle fa-3x text-muted mb-3"></i>
                                    <p>No questions found.</p>
                                    <a href="{{ route('questions.create') }}" class="btn btn-primary">Add Question</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $questions->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const subjectSelect = document.getElementById('subject_id');
        const unitSelect = document.getElementById('unit_id');
        const topicSelect = document.getElementById('topic_id');
        
        // Subject change event
        subjectSelect.addEventListener('change', function() {
            const subjectId = this.value;
            
            // Clear unit and topic selects
            unitSelect.innerHTML = '<option value="">All Units</option>';
            topicSelect.innerHTML = '<option value="">All Topics</option>';
            
            if (subjectId) {
                // Fetch units for the selected subject
                fetch(`/api/subjects/${subjectId}/units`)
                    .then(response => response.json())
                    .then(units => {
                        units.forEach(unit => {
                            const option = document.createElement('option');
                            option.value = unit.id;
                            option.textContent = unit.unit_name;
                            unitSelect.appendChild(option);
                        });
                    });
            }
        });
        
        // Unit change event
        unitSelect.addEventListener('change', function() {
            const unitId = this.value;
            
            // Clear topic select
            topicSelect.innerHTML = '<option value="">All Topics</option>';
            
            if (unitId) {
                // Fetch topics for the selected unit
                fetch(`/api/units/${unitId}/topics`)
                    .then(response => response.json())
                    .then(topics => {
                        topics.forEach(topic => {
                            const option = document.createElement('option');
                            option.value = topic.id;
                            option.textContent = topic.topic_name;
                            topicSelect.appendChild(option);
                        });
                    });
            }
        });
    });
</script>
@endpush
@endsection

