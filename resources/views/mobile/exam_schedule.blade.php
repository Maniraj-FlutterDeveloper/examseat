@extends('layouts.mobile')

@section('title', 'Exam Schedule')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-calendar-alt me-2"></i> My Exam Schedule
            </div>
            <div class="card-body p-0">
                @if($examSchedule->count() > 0)
                    <div class="list-group list-group-flush">
                        @php
                            $currentDate = null;
                        @endphp
                        
                        @foreach($examSchedule as $exam)
                            @if($currentDate !== $exam->exam_date->format('Y-m-d'))
                                @php
                                    $currentDate = $exam->exam_date->format('Y-m-d');
                                @endphp
                                <div class="list-group-item bg-light">
                                    <h5 class="mb-0">{{ $exam->exam_date->format('l, F j, Y') }}</h5>
                                </div>
                            @endif
                            
                            <a href="{{ route('mobile.seating_plans.view', $exam->id) }}" class="list-group-item list-group-item-action exam-card">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $exam->title }}</h5>
                                    <small class="text-muted">{{ $exam->exam_date->format('h:i A') }}</small>
                                </div>
                                <p class="mb-1">{{ $exam->subject }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Duration: {{ $exam->duration }} minutes</small>
                                    <span class="badge {{ $exam->exam_date->isPast() ? 'bg-secondary' : 'bg-primary' }}">
                                        {{ $exam->exam_date->isPast() ? 'Completed' : 'Upcoming' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No exams scheduled.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

