<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dashboard;
use App\Models\DashboardWidget;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $analyticsService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\AnalyticsService  $analyticsService
     * @return void
     */
    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    /**
     * Display the analytics dashboard.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function analytics(Request $request)
    {
        // Get the user's default dashboard or create one if it doesn't exist
        $dashboard = Dashboard::where('user_id', Auth::id())
            ->where('is_default', true)
            ->first();
        
        if (!$dashboard) {
            $dashboard = Dashboard::create([
                'user_id' => Auth::id(),
                'name' => 'Default Dashboard',
                'layout' => ['type' => 'grid', 'columns' => 3],
                'is_default' => true,
                'is_public' => false,
            ]);
            
            // Create default widgets
            $this->createDefaultWidgets($dashboard);
        }
        
        // Get the dashboard widgets
        $widgets = DashboardWidget::where('dashboard_id', $dashboard->id)
            ->orderBy('created_at')
            ->get();
        
        // Get the widget data
        $widgetData = [];
        foreach ($widgets as $widget) {
            $widgetData[$widget->id] = $this->analyticsService->getWidgetData($widget);
        }
        
        // Get all dashboards for the user
        $dashboards = Dashboard::where('user_id', Auth::id())
            ->orWhere('is_public', true)
            ->orderBy('name')
            ->get();
        
        return view('admin.analytics.dashboard', compact('dashboard', 'widgets', 'widgetData', 'dashboards'));
    }

    /**
     * Show the form for creating a new dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function createDashboard()
    {
        $layouts = Dashboard::getLayouts();
        
        return view('admin.analytics.create_dashboard', compact('layouts'));
    }

    /**
     * Store a newly created dashboard in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeDashboard(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'layout_type' => 'required|string|in:' . implode(',', array_keys(Dashboard::getLayouts())),
            'columns' => 'required|integer|min:1|max:4',
        ]);
        
        $dashboard = Dashboard::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'layout' => [
                'type' => $request->layout_type,
                'columns' => $request->columns,
            ],
            'is_default' => $request->has('is_default'),
            'is_public' => $request->has('is_public'),
        ]);
        
        // If this is set as default, update other dashboards
        if ($request->has('is_default')) {
            Dashboard::where('user_id', Auth::id())
                ->where('id', '!=', $dashboard->id)
                ->update(['is_default' => false]);
        }
        
        return redirect()->route('admin.analytics.dashboard', ['dashboard_id' => $dashboard->id])
            ->with('success', 'Dashboard created successfully.');
    }

    /**
     * Show the form for editing the specified dashboard.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editDashboard($id)
    {
        $dashboard = Dashboard::findOrFail($id);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $layouts = Dashboard::getLayouts();
        
        return view('admin.analytics.edit_dashboard', compact('dashboard', 'layouts'));
    }

    /**
     * Update the specified dashboard in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateDashboard(Request $request, $id)
    {
        $dashboard = Dashboard::findOrFail($id);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'layout_type' => 'required|string|in:' . implode(',', array_keys(Dashboard::getLayouts())),
            'columns' => 'required|integer|min:1|max:4',
        ]);
        
        $dashboard->update([
            'name' => $request->name,
            'layout' => [
                'type' => $request->layout_type,
                'columns' => $request->columns,
            ],
            'is_default' => $request->has('is_default'),
            'is_public' => $request->has('is_public'),
        ]);
        
        // If this is set as default, update other dashboards
        if ($request->has('is_default')) {
            Dashboard::where('user_id', Auth::id())
                ->where('id', '!=', $dashboard->id)
                ->update(['is_default' => false]);
        }
        
        return redirect()->route('admin.analytics.dashboard', ['dashboard_id' => $dashboard->id])
            ->with('success', 'Dashboard updated successfully.');
    }

    /**
     * Remove the specified dashboard from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyDashboard($id)
    {
        $dashboard = Dashboard::findOrFail($id);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $dashboard->delete();
        
        return redirect()->route('admin.analytics.index')
            ->with('success', 'Dashboard deleted successfully.');
    }

    /**
     * Show the form for creating a new widget.
     *
     * @param  int  $dashboardId
     * @return \Illuminate\Http\Response
     */
    public function createWidget($dashboardId)
    {
        $dashboard = Dashboard::findOrFail($dashboardId);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $types = DashboardWidget::getTypes();
        $sizes = DashboardWidget::getSizes();
        $chartTypes = DashboardWidget::getChartTypes();
        
        return view('admin.analytics.create_widget', compact('dashboard', 'types', 'sizes', 'chartTypes'));
    }

    /**
     * Store a newly created widget in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $dashboardId
     * @return \Illuminate\Http\Response
     */
    public function storeWidget(Request $request, $dashboardId)
    {
        $dashboard = Dashboard::findOrFail($dashboardId);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(DashboardWidget::getTypes())),
            'size' => 'required|string|in:' . implode(',', array_keys(DashboardWidget::getSizes())),
            'refresh_interval' => 'nullable|integer|min:0',
            'config' => 'nullable|array',
        ]);
        
        $widget = DashboardWidget::create([
            'dashboard_id' => $dashboard->id,
            'title' => $request->title,
            'type' => $request->type,
            'size' => $request->size,
            'position' => $request->position ?? null,
            'config' => $request->config,
            'refresh_interval' => $request->refresh_interval ?? 0,
        ]);
        
        return redirect()->route('admin.analytics.dashboard', ['dashboard_id' => $dashboard->id])
            ->with('success', 'Widget added successfully.');
    }

    /**
     * Show the form for editing the specified widget.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function editWidget($id)
    {
        $widget = DashboardWidget::with('dashboard')->findOrFail($id);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($widget->dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $types = DashboardWidget::getTypes();
        $sizes = DashboardWidget::getSizes();
        $chartTypes = DashboardWidget::getChartTypes();
        
        return view('admin.analytics.edit_widget', compact('widget', 'types', 'sizes', 'chartTypes'));
    }

    /**
     * Update the specified widget in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateWidget(Request $request, $id)
    {
        $widget = DashboardWidget::with('dashboard')->findOrFail($id);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($widget->dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(DashboardWidget::getTypes())),
            'size' => 'required|string|in:' . implode(',', array_keys(DashboardWidget::getSizes())),
            'refresh_interval' => 'nullable|integer|min:0',
            'config' => 'nullable|array',
        ]);
        
        $widget->update([
            'title' => $request->title,
            'type' => $request->type,
            'size' => $request->size,
            'position' => $request->position ?? $widget->position,
            'config' => $request->config,
            'refresh_interval' => $request->refresh_interval ?? 0,
        ]);
        
        return redirect()->route('admin.analytics.dashboard', ['dashboard_id' => $widget->dashboard_id])
            ->with('success', 'Widget updated successfully.');
    }

    /**
     * Remove the specified widget from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroyWidget($id)
    {
        $widget = DashboardWidget::with('dashboard')->findOrFail($id);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($widget->dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $dashboardId = $widget->dashboard_id;
        $widget->delete();
        
        return redirect()->route('admin.analytics.dashboard', ['dashboard_id' => $dashboardId])
            ->with('success', 'Widget removed successfully.');
    }

    /**
     * Update the widget positions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $dashboardId
     * @return \Illuminate\Http\Response
     */
    public function updateWidgetPositions(Request $request, $dashboardId)
    {
        $dashboard = Dashboard::findOrFail($dashboardId);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($dashboard->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'positions' => 'required|array',
            'positions.*.id' => 'required|integer|exists:dashboard_widgets,id',
            'positions.*.position' => 'required|array',
        ]);
        
        foreach ($request->positions as $position) {
            $widget = DashboardWidget::find($position['id']);
            
            if ($widget && $widget->dashboard_id == $dashboardId) {
                $widget->update(['position' => $position['position']]);
            }
        }
        
        return response()->json(['success' => true]);
    }

    /**
     * Get the widget data.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getWidgetData(Request $request, $id)
    {
        $widget = DashboardWidget::with('dashboard')->findOrFail($id);
        
        // Check if the dashboard belongs to the authenticated user or user is admin
        if ($widget->dashboard->user_id !== Auth::id() && !$widget->dashboard->is_public && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $data = $this->analyticsService->getWidgetData($widget);
        
        return response()->json($data);
    }

    /**
     * Create default widgets for a dashboard.
     *
     * @param  \App\Models\Dashboard  $dashboard
     * @return void
     */
    protected function createDefaultWidgets($dashboard)
    {
        // Create a few default widgets
        DashboardWidget::create([
            'dashboard_id' => $dashboard->id,
            'type' => 'metric',
            'title' => 'Total Students',
            'size' => 'small',
            'position' => ['x' => 0, 'y' => 0, 'w' => 1, 'h' => 1],
            'config' => [
                'metric_type' => 'total_students',
                'icon' => 'user-graduate',
                'color' => 'primary',
            ],
        ]);
        
        DashboardWidget::create([
            'dashboard_id' => $dashboard->id,
            'type' => 'metric',
            'title' => 'Total Rooms',
            'size' => 'small',
            'position' => ['x' => 1, 'y' => 0, 'w' => 1, 'h' => 1],
            'config' => [
                'metric_type' => 'total_rooms',
                'icon' => 'door-open',
                'color' => 'success',
            ],
        ]);
        
        DashboardWidget::create([
            'dashboard_id' => $dashboard->id,
            'type' => 'metric',
            'title' => 'Total Courses',
            'size' => 'small',
            'position' => ['x' => 2, 'y' => 0, 'w' => 1, 'h' => 1],
            'config' => [
                'metric_type' => 'total_courses',
                'icon' => 'graduation-cap',
                'color' => 'info',
            ],
        ]);
        
        DashboardWidget::create([
            'dashboard_id' => $dashboard->id,
            'type' => 'chart',
            'title' => 'Students by Course',
            'size' => 'medium',
            'position' => ['x' => 0, 'y' => 1, 'w' => 2, 'h' => 2],
            'config' => [
                'chart_type' => 'pie',
                'data_source' => 'students_by_course',
            ],
        ]);
        
        DashboardWidget::create([
            'dashboard_id' => $dashboard->id,
            'type' => 'chart',
            'title' => 'Rooms by Block',
            'size' => 'medium',
            'position' => ['x' => 2, 'y' => 1, 'w' => 1, 'h' => 2],
            'config' => [
                'chart_type' => 'bar',
                'data_source' => 'rooms_by_block',
            ],
        ]);
        
        DashboardWidget::create([
            'dashboard_id' => $dashboard->id,
            'type' => 'table',
            'title' => 'Recent Seating Plans',
            'size' => 'large',
            'position' => ['x' => 0, 'y' => 3, 'w' => 3, 'h' => 2],
            'config' => [
                'data_source' => 'recent_seating_plans',
                'columns' => ['id', 'name', 'created_at', 'status'],
            ],
        ]);
    }
}

