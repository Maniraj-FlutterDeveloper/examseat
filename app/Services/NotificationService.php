<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Send a notification to a specific user.
     *
     * @param  int  $userId
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  string|null  $link
     * @param  array|null  $data
     * @return \App\Models\Notification
     */
    public function sendToUser($userId, $title, $message, $type = 'info', $link = null, $data = null)
    {
        return Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'data' => $data,
        ]);
    }

    /**
     * Send a notification to the authenticated user.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  string|null  $link
     * @param  array|null  $data
     * @return \App\Models\Notification|null
     */
    public function sendToAuth($title, $message, $type = 'info', $link = null, $data = null)
    {
        if (Auth::check()) {
            return $this->sendToUser(Auth::id(), $title, $message, $type, $link, $data);
        }
        
        return null;
    }

    /**
     * Send a notification to multiple users.
     *
     * @param  array  $userIds
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  string|null  $link
     * @param  array|null  $data
     * @return array
     */
    public function sendToMultiple($userIds, $title, $message, $type = 'info', $link = null, $data = null)
    {
        $notifications = [];
        
        foreach ($userIds as $userId) {
            $notifications[] = $this->sendToUser($userId, $title, $message, $type, $link, $data);
        }
        
        return $notifications;
    }

    /**
     * Send a notification to all users.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  string|null  $link
     * @param  array|null  $data
     * @return array
     */
    public function sendToAll($title, $message, $type = 'info', $link = null, $data = null)
    {
        $users = User::all();
        $notifications = [];
        
        foreach ($users as $user) {
            $notifications[] = $this->sendToUser($user->id, $title, $message, $type, $link, $data);
        }
        
        return $notifications;
    }

    /**
     * Send a notification to users with a specific role.
     *
     * @param  string  $role
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  string|null  $link
     * @param  array|null  $data
     * @return array
     */
    public function sendToRole($role, $title, $message, $type = 'info', $link = null, $data = null)
    {
        $users = User::where('role', $role)->get();
        $notifications = [];
        
        foreach ($users as $user) {
            $notifications[] = $this->sendToUser($user->id, $title, $message, $type, $link, $data);
        }
        
        return $notifications;
    }

    /**
     * Send a system notification.
     *
     * @param  string  $title
     * @param  string  $message
     * @param  string  $type
     * @param  string|null  $link
     * @param  array|null  $data
     * @return array
     */
    public function sendSystemNotification($title, $message, $type = 'info', $link = null, $data = null)
    {
        return $this->sendToRole('admin', $title, $message, $type, $link, $data);
    }

    /**
     * Get unread notifications count for a user.
     *
     * @param  int|null  $userId
     * @return int
     */
    public function getUnreadCount($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::where('user_id', $userId)->unread()->count();
    }

    /**
     * Get recent notifications for a user.
     *
     * @param  int|null  $userId
     * @param  int  $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentNotifications($userId = null, $limit = 5)
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Mark all notifications as read for a user.
     *
     * @param  int|null  $userId
     * @return int
     */
    public function markAllAsRead($userId = null)
    {
        $userId = $userId ?? Auth::id();
        
        return Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}

