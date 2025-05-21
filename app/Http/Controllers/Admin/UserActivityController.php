<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;

class UserActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = UserActivity::with('user');

        // Apply filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('user_id') && $request->input('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('action') && $request->input('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->has('module') && $request->input('module')) {
            $query->where('module', $request->input('module'));
        }

        if ($request->has('date_from') && $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to') && $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        // Sort
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $activities = $query->paginate(20);
        $users = User::all();
        $actions = UserActivity::getActions();
        $modules = UserActivity::getModules();

        return view('admin.activities.index', [
            'activities' => $activities,
            'users' => $users,
            'actions' => $actions,
            'modules' => $modules,
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $activity = UserActivity::with('user')->findOrFail($id);

        return view('admin.activities.show', [
            'activity' => $activity,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $activity = UserActivity::findOrFail($id);
        $activity->delete();

        return redirect()->route('admin.activities.index')
            ->with('success', 'Activity log entry deleted successfully.');
    }

    /**
     * Clear all activity logs.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearAll()
    {
        UserActivity::truncate();

        // Log this action
        auth()->user()->logActivity(
            'delete',
            'activities',
            'Cleared all activity logs',
            []
        );

        return redirect()->route('admin.activities.index')
            ->with('success', 'All activity logs cleared successfully.');
    }

    /**
     * Export activity logs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $query = UserActivity::with('user');

        // Apply filters
        if ($request->has('user_id') && $request->input('user_id')) {
            $query->where('user_id', $request->input('user_id'));
        }

        if ($request->has('action') && $request->input('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->has('module') && $request->input('module')) {
            $query->where('module', $request->input('module'));
        }

        if ($request->has('date_from') && $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->has('date_to') && $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        // Log this action
        auth()->user()->logActivity(
            'export',
            'activities',
            'Exported activity logs',
            [
                'filters' => $request->only(['user_id', 'action', 'module', 'date_from', 'date_to']),
                'count' => $activities->count(),
            ]
        );

        // Generate CSV
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity_logs_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($activities) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'User', 'Email', 'Action', 'Module', 'Description', 'IP Address', 'Date']);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->id,
                    $activity->user->name ?? 'Unknown',
                    $activity->user->email ?? 'Unknown',
                    $activity->action,
                    $activity->module,
                    $activity->description,
                    $activity->ip_address,
                    $activity->created_at,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

