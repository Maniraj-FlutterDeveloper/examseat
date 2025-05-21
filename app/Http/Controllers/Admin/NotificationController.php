<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Notification::where('user_id', Auth::id());
        
        // Filter by read status
        if ($request->has('status')) {
            if ($request->status === 'read') {
                $query->read();
            } elseif ($request->status === 'unread') {
                $query->unread();
            }
        }
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }
        
        // Order by created_at desc
        $notifications = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get notification types for filter
        $types = Notification::select('type')->distinct()->pluck('type');
        
        return view('admin.notifications.index', compact('notifications', 'types'));
    }

    /**
     * Display the specified notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        // Mark as read if not already read
        if (!$notification->isRead()) {
            $notification->markAsRead();
        }
        
        return view('admin.notifications.show', compact('notification'));
    }

    /**
     * Mark a notification as read.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsRead($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->markAsRead();
        
        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    /**
     * Mark a notification as unread.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function markAsUnread($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->markAsUnread();
        
        return redirect()->back()->with('success', 'Notification marked as unread.');
    }

    /**
     * Mark all notifications as read.
     *
     * @return \Illuminate\Http\Response
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        
        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        
        // Check if the notification belongs to the authenticated user
        if ($notification->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }
        
        $notification->delete();
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notification deleted successfully.');
    }

    /**
     * Delete all read notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearRead()
    {
        Notification::where('user_id', Auth::id())
            ->whereNotNull('read_at')
            ->delete();
        
        return redirect()->route('admin.notifications.index')->with('success', 'All read notifications cleared.');
    }

    /**
     * Get unread notifications count.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUnreadCount()
    {
        $count = Notification::where('user_id', Auth::id())->unread()->count();
        
        return response()->json(['count' => $count]);
    }

    /**
     * Get recent unread notifications.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRecentUnread()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->unread()
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json(['notifications' => $notifications]);
    }

    /**
     * Create a new notification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:info,success,warning,error',
            'link' => 'nullable|string|max:255',
            'data' => 'nullable|json',
        ]);
        
        $notification = Notification::create($request->all());
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notification created successfully.');
    }

    /**
     * Send a notification to multiple users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendToMultiple(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:info,success,warning,error',
            'link' => 'nullable|string|max:255',
            'data' => 'nullable|json',
        ]);
        
        $notificationData = [
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'link' => $request->link,
            'data' => $request->data,
        ];
        
        foreach ($request->user_ids as $userId) {
            $notificationData['user_id'] = $userId;
            Notification::create($notificationData);
        }
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notifications sent successfully.');
    }

    /**
     * Send a notification to all users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendToAll(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|in:info,success,warning,error',
            'link' => 'nullable|string|max:255',
            'data' => 'nullable|json',
        ]);
        
        $users = User::all();
        
        $notificationData = [
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'link' => $request->link,
            'data' => $request->data,
        ];
        
        foreach ($users as $user) {
            $notificationData['user_id'] = $user->id;
            Notification::create($notificationData);
        }
        
        return redirect()->route('admin.notifications.index')->with('success', 'Notifications sent to all users.');
    }
}

