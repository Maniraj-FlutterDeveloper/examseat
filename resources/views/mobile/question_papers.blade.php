@extends('layouts.mobile')

@section('title', 'Question Papers')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-alt me-2"></i> My Question Papers
            </div>
            <div class="card-body p-0">
                @if($questionPapers->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($questionPapers as $questionPaper)
                            <a href="{{ route('mobile.question_papers.view', $questionPaper->id) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $questionPaper->title }}</h5>
                                    <small class="text-muted">{{ $questionPaper->created_at->format('M d, Y') }}</small>
                                </div>
                                <p class="mb-1">{{ $questionPaper->subject->name }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="fas fa-question-circle me-1"></i> {{ $questionPaper->questions->count() }} Questions
                                        <i class="fas fa-clock ms-2 me-1"></i> {{ $questionPaper->duration }} minutes
                                        <i class="fas fa-star ms-2 me-1"></i> {{ $questionPaper->total_marks }} Marks
                                    </small>
                                    <span class="badge bg-primary">
                                        <i class="fas fa-file-pdf me-1"></i> View
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-alt text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No question papers available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

