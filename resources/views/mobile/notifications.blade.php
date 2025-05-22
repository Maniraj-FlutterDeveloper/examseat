@extends('layouts.mobile')

@section('title', 'Notifications')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bell me-2"></i> My Notifications
            </div>
            <div class="card-body p-0">
                @if($notifications->count() > 0)
                    <div class="list-group list-group-flush">
                        @foreach($notifications as $notification)
                            <a href="{{ route('mobile.notifications.view', $notification->id) }}" class="list-group-item list-group-item-action notification-card {{ $notification->read_at ? '' : 'unread' }}">
                                <div class="d-flex w-100 justify-content-between">
                                    <h5 class="mb-1">{{ $notification->title }}</h5>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="mb-1">{{ Str::limit($notification->message, 100) }}</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">{{ $notification->created_at->format('M d, Y h:i A') }}</small>
                                    @if(!$notification->read_at)
                                        <span class="badge bg-primary">New</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                    
                    <div class="d-flex justify-content-center py-3">
                        {{ $notifications->links() }}
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
@endsection

