@extends('layouts.mobile')

@section('title', 'View Notification')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bell me-2"></i> Notification Details
            </div>
            <div class="card-body">
                <h4 class="mb-3">{{ $notification->title }}</h4>
                
                <div class="d-flex justify-content-between text-muted mb-4">
                    <div>
                        <i class="fas fa-clock me-1"></i> {{ $notification->created_at->format('M d, Y h:i A') }}
                    </div>
                    <div>
                        @if($notification->read_at)
                            <i class="fas fa-check-double me-1"></i> Read {{ $notification->read_at->diffForHumans() }}
                        @else
                            <i class="fas fa-check me-1"></i> Unread
                        @endif
                    </div>
                </div>
                
                <div class="notification-content mb-4">
                    {!! nl2br(e($notification->message)) !!}
                </div>
                
                @if($notification->action_url)
                    <div class="d-grid gap-2">
                        <a href="{{ $notification->action_url }}" class="btn btn-primary">
                            <i class="fas fa-external-link-alt me-2"></i> {{ $notification->action_text ?? 'View Details' }}
                        </a>
                    </div>
                @endif
                
                <hr>
                
                <div class="d-flex justify-content-between">
                    <a href="{{ route('mobile.notifications') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i> Back to Notifications
                    </a>
                    
                    @if($notification->read_at)
                        <span class="text-muted">
                            <i class="fas fa-check-double me-1"></i> Read
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

