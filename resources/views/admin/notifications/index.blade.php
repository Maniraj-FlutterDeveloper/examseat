@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Notifications</h1>
        <div>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#sendNotificationModal">
                <i class="fas fa-bell me-2"></i>Send Notification
            </button>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Filters</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.notifications.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All</option>
                                <option value="read" {{ request('status') == 'read' ? 'selected' : '' }}>Read</option>
                                <option value="unread" {{ request('status') == 'unread' ? 'selected' : '' }}>Unread</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="type" class="form-label">Type</label>
                            <select class="form-select" id="type" name="type">
                                <option value="">All</option>
                                @foreach($types as $type)
                                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-filter me-2"></i>Filter
                            </button>
                            <a href="{{ route('admin.notifications.index') }}" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Notifications</h5>
                    <div>
                        <form action="{{ route('admin.notifications.mark_all_as_read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-check-double me-1"></i>Mark All as Read
                            </button>
                        </form>
                        <form action="{{ route('admin.notifications.clear_read') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to clear all read notifications?')">
                                <i class="fas fa-trash me-1"></i>Clear Read
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if($notifications->count() > 0)
                        <div class="list-group">
                            @foreach($notifications as $notification)
                                <a href="{{ route('admin.notifications.show', $notification->id) }}" class="list-group-item list-group-item-action {{ $notification->read_at ? '' : 'list-group-item-light' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1">
                                            @if(!$notification->read_at)
                                                <span class="badge bg-primary me-2">New</span>
                                            @endif
                                            {{ $notification->title }}
                                        </h5>
                                        <small>{{ $notification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($notification->message, 100) }}</p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            <span class="badge bg-{{ $notification->type == 'info' ? 'info' : ($notification->type == 'success' ? 'success' : ($notification->type == 'warning' ? 'warning' : 'danger')) }}">
                                                {{ ucfirst($notification->type) }}
                                            </span>
                                        </small>
                                        <div>
                                            @if($notification->read_at)
                                                <a href="{{ route('admin.notifications.mark_as_unread', $notification->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    <i class="fas fa-envelope me-1"></i>Mark as Unread
                                                </a>
                                            @else
                                                <a href="{{ route('admin.notifications.mark_as_read', $notification->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-envelope-open me-1"></i>Mark as Read
                                                </a>
                                            @endif
                                            <form action="{{ route('admin.notifications.destroy', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this notification?')">
                                                    <i class="fas fa-trash me-1"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        
                        <div class="mt-4">
                            {{ $notifications->appends(request()->except('page'))->links() }}
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No notifications found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Send Notification Modal -->
<div class="modal fade" id="sendNotificationModal" tabindex="-1" aria-labelledby="sendNotificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sendNotificationModalLabel">Send Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="notificationTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="single-tab" data-bs-toggle="tab" data-bs-target="#single" type="button" role="tab" aria-controls="single" aria-selected="true">
                            <i class="fas fa-user me-2"></i>Single User
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="multiple-tab" data-bs-toggle="tab" data-bs-target="#multiple" type="button" role="tab" aria-controls="multiple" aria-selected="false">
                            <i class="fas fa-users me-2"></i>Multiple Users
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="false">
                            <i class="fas fa-globe me-2"></i>All Users
                        </button>
                    </li>
                </ul>
                <div class="tab-content mt-3" id="notificationTabsContent">
                    <!-- Single User Tab -->
                    <div class="tab-pane fade show active" id="single" role="tabpanel" aria-labelledby="single-tab">
                        <form action="{{ route('admin.notifications.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="user_id" class="form-label">User <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_id" name="user_id" required>
                                    <option value="">Select User</option>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="link" class="form-label">Link</label>
                                <input type="text" class="form-control" id="link" name="link">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Notification
                            </button>
                        </form>
                    </div>
                    
                    <!-- Multiple Users Tab -->
                    <div class="tab-pane fade" id="multiple" role="tabpanel" aria-labelledby="multiple-tab">
                        <form action="{{ route('admin.notifications.send_to_multiple') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="user_ids" class="form-label">Users <span class="text-danger">*</span></label>
                                <select class="form-select" id="user_ids" name="user_ids[]" multiple required>
                                    @foreach(\App\Models\User::all() as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                <div class="form-text">Hold Ctrl (or Cmd on Mac) to select multiple users.</div>
                            </div>
                            <div class="mb-3">
                                <label for="multiple_title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="multiple_title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="multiple_message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="multiple_message" name="message" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="multiple_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="multiple_type" name="type" required>
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="multiple_link" class="form-label">Link</label>
                                <input type="text" class="form-control" id="multiple_link" name="link">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Notifications
                            </button>
                        </form>
                    </div>
                    
                    <!-- All Users Tab -->
                    <div class="tab-pane fade" id="all" role="tabpanel" aria-labelledby="all-tab">
                        <form action="{{ route('admin.notifications.send_to_all') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="all_title" class="form-label">Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="all_title" name="title" required>
                            </div>
                            <div class="mb-3">
                                <label for="all_message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="all_message" name="message" rows="3" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="all_type" class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select" id="all_type" name="type" required>
                                    <option value="info">Info</option>
                                    <option value="success">Success</option>
                                    <option value="warning">Warning</option>
                                    <option value="error">Error</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="all_link" class="form-label">Link</label>
                                <input type="text" class="form-control" id="all_link" name="link">
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send to All Users
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize select2 for user selection if available
        if (typeof $.fn.select2 !== 'undefined') {
            $('#user_id').select2({
                placeholder: 'Select User',
                dropdownParent: $('#sendNotificationModal')
            });
            
            $('#user_ids').select2({
                placeholder: 'Select Users',
                dropdownParent: $('#sendNotificationModal')
            });
        }
    });
</script>
@endpush

