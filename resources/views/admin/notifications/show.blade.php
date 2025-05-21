@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Notification Details</h1>
        <div>
            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Notifications
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">{{ $notification->title }}</h5>
                    <span class="badge bg-{{ $notification->type == 'info' ? 'info' : ($notification->type == 'success' ? 'success' : ($notification->type == 'warning' ? 'warning' : 'danger')) }}">
                        {{ ucfirst($notification->type) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <p class="card-text">{{ $notification->message }}</p>
                    </div>
                    
                    @if($notification->link)
                        <div class="mb-4">
                            <h6>Link:</h6>
                            <a href="{{ $notification->link }}" target="_blank">{{ $notification->link }}</a>
                        </div>
                    @endif
                    
                    @if($notification->data)
                        <div class="mb-4">
                            <h6>Additional Data:</h6>
                            <pre class="bg-light p-3 rounded">{{ json_encode($notification->data, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6>Created:</h6>
                            <p>{{ $notification->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Read:</h6>
                            <p>
                                @if($notification->read_at)
                                    {{ $notification->read_at->format('M d, Y h:i A') }}
                                @else
                                    <span class="text-muted">Not read yet</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            @if($notification->read_at)
                                <a href="{{ route('admin.notifications.mark_as_unread', $notification->id) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-envelope me-2"></i>Mark as Unread
                                </a>
                            @else
                                <a href="{{ route('admin.notifications.mark_as_read', $notification->id) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-envelope-open me-2"></i>Mark as Read
                                </a>
                            @endif
                        </div>
                        <div>
                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this notification?')">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

