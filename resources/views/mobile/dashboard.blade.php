@extends('layouts.mobile')

@section('title', 'Dashboard')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <img src="{{ $student->profile_picture_url }}" alt="{{ $student->name }}" class="profile-picture mb-3">
                <h4>{{ $student->name }}</h4>
                <p class="text-muted mb-1">{{ $student->roll_number }}</p>
                <p class="text-muted mb-3">{{ $student->course->name }} - Year {{ $student->year }}</p>
                <a href="{{ route('mobile.profile') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-user-edit me-1"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-alt me-2"></i> Upcoming Exams</span>
                <a href="{{ route('mobile.exam_schedule') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                @if($upcomingExams->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($upcomingExams as $exam)
                            <div class="list-group-item exam-card">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $exam->title }}</h5>
                                    <small class="text-muted">{{ $exam->exam_date->format('d M Y') }}</small>
                                </div>
                                <p class="mb-1">{{ $exam->subject }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $exam->exam_date->format('h:i A') }} - {{ $exam->end_time->format('h:i A') }}</small>
                                    <a href="{{ route('mobile.seating_plans.view', $exam->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-chair me-1"></i> View Seat
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-check text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No upcoming exams scheduled.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-bell me-2"></i> Recent Notifications</span>
                <a href="{{ route('mobile.notifications') }}" class="btn btn-sm btn-light">View All</a>
            </div>
            <div class="card-body p-0">
                @if($recentNotifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($recentNotifications as $notification)
                            <a href="{{ route('mobile.notifications.view', $notification->id) }}" class="list-group-item list-group-item-action notification-card {{ $notification->read_at ? '' : 'unread' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $notification->title }}</h5>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ Str::limit($notification->message, 100) }}</p>
                                @if(!$notification->read_at)
                                    <span class="badge bg-primary">New</span>
                                @endif
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-bell-slash text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted">No notifications yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-6">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="fas fa-chair text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h5>Seating Plans</h5>
                <p class="text-muted">View your exam seating arrangements</p>
                <a href="{{ route('mobile.seating_plans') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-1"></i> View
                </a>
            </div>
        </div>
    </div>
    <div class="col-6">
        <div class="card h-100">
            <div class="card-body text-center py-4">
                <i class="fas fa-file-alt text-primary mb-3" style="font-size: 2.5rem;"></i>
                <h5>Question Papers</h5>
                <p class="text-muted">Access your exam question papers</p>
                <a href="{{ route('mobile.question_papers') }}" class="btn btn-primary">
                    <i class="fas fa-arrow-right me-1"></i> View
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

