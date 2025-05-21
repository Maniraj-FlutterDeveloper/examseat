@extends('layouts.mobile')

@section('title', 'Seating Plans')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chair me-2"></i> My Seating Plans
            </div>
            <div class="card-body p-0">
                @if($seatingPlans->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($seatingPlans as $seatingPlan)
                            <a href="{{ route('mobile.seating_plans.view', $seatingPlan->id) }}" class="list-group-item list-group-item-action exam-card">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $seatingPlan->title }}</h5>
                                    <small class="text-muted">{{ $seatingPlan->exam_date->format('d M Y') }}</small>
                                </div>
                                <p class="mb-1">{{ $seatingPlan->subject }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $seatingPlan->exam_date->format('h:i A') }} - {{ $seatingPlan->end_time->format('h:i A') }}</small>
                                    <span class="badge {{ $seatingPlan->exam_date->isPast() ? 'bg-secondary' : 'bg-primary' }}">
                                        {{ $seatingPlan->exam_date->isPast() ? 'Completed' : 'Upcoming' }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-chair text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No seating plans available.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

