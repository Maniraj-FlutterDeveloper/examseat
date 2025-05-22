@extends('layouts.question-bank')

@section('title', 'Question Papers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Question Papers</h1>
    <div>
        <a href="{{ route('question-bank.question-papers.create') }}" class="btn btn-primary me-2">
            <i class="fas fa-plus"></i> Create from Blueprint
        </a>
        <a href="{{ route('question-bank.question-papers.create-random') }}" class="btn btn-success">
            <i class="fas fa-random"></i> Generate Random
        </a>
    </div>
</div>

<div class="card shadow mb-4 fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Search & Filter</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('question-bank.question-papers.index') }}" method="GET" id="filter-form">
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
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="blueprint_id" class="form-label">Blueprint</label>
                    <select class="form-select" id="blueprint_id" name="blueprint_id">
                        <option value="">All Blueprints</option>
                        <option value="none" {{ request('blueprint_id') === 'none' ? 'selected' : '' }}>No Blueprint (Random)</option>
                        @foreach($blueprints as $blueprint)
                            <option value="{{ $blueprint->id }}" {{ request('blueprint_id') == $blueprint->id ? 'selected' : '' }}>
                                {{ $blueprint->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-search"></i> Search
                    </button>
                    <a href="{{ route('question-bank.question-papers.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">All Question Papers</h6>
        <span class="badge bg-primary">{{ $questionPapers->total() }} Papers</span>
    </div>
    <div class="card-body">
        @if($questionPapers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Total Marks</th>
                            <th>Questions</th>
                            <th>Blueprint</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($questionPapers as $paper)
                            <tr>
                                <td>
                                    <a href="{{ route('question-bank.question-papers.show', $paper) }}" class="fw-bold text-decoration-none">
                                        {{ $paper->title }}
                                    </a>
                                </td>
                                <td>{{ $paper->subject->name ?? 'Multiple Subjects' }}</td>
                                <td>{{ $paper->total_marks }}</td>
                                <td>{{ $paper->questions_count }}</td>
                                <td>
                                    @if($paper->blueprint)
                                        <a href="{{ route('question-bank.blueprints.show', $paper->blueprint) }}">
                                            {{ $paper->blueprint->title }}
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">Random</span>
                                    @endif
                                </td>
                                <td>
                                    @if($paper->status === 'draft')
                                        <span class="badge bg-secondary">Draft</span>
                                    @elseif($paper->status === 'published')
                                        <span class="badge bg-success">Published</span>
                                    @elseif($paper->status === 'archived')
                                        <span class="badge bg-warning">Archived</span>
                                    @endif
                                </td>
                                <td>{{ $paper->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('question-bank.question-papers.show', $paper) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('question-bank.question-papers.edit', $paper) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('question-bank.question-papers.export', $paper) }}" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Export PDF">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
                                        <form action="{{ route('question-bank.question-papers.destroy', $paper) }}" method="POST" class="d-inline delete-form">
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
                {{ $questionPapers->appends(request()->except('page'))->links() }}
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No question papers found matching your criteria. 
                <a href="{{ route('question-bank.question-papers.create') }}" class="alert-link">Create a new question paper</a> or 
                <a href="{{ route('question-bank.question-papers.index') }}" class="alert-link">reset filters</a>.
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
        if (confirm('Are you sure you want to delete this question paper? This action cannot be undone.')) {
            this.submit();
        }
    });
</script>
@endpush

