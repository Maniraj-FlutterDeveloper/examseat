<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportResult;
use App\Services\ReportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    protected $reportService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\ReportService  $reportService
     * @return void
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display a listing of the reports.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Report::query();
        
        // Filter by type
        if ($request->has('type') && $request->type) {
            $query->ofType($request->type);
        }
        
        // Filter by favorite
        if ($request->has('favorite') && $request->favorite) {
            $query->favorites();
        }
        
        // Filter by user
        if (Auth::user()->role === 'admin' && $request->has('user_id') && $request->user_id) {
            $query->byUser($request->user_id);
        } else {
            $query->byUser(Auth::id());
        }
        
        // Order by created_at desc
        $reports = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Get report types for filter
        $types = Report::getTypes();
        
        return view('admin.reports.index', compact('reports', 'types'));
    }

    /**
     * Show the form for creating a new report.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $types = Report::getTypes();
        
        return view('admin.reports.create', compact('types'));
    }

    /**
     * Store a newly created report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(Report::getTypes())),
            'parameters' => 'nullable|array',
            'schedule' => 'nullable|array',
        ]);
        
        $report = Report::create([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'user_id' => Auth::id(),
            'parameters' => $request->parameters,
            'schedule' => $request->schedule,
            'is_favorite' => $request->has('is_favorite'),
        ]);
        
        return redirect()->route('admin.reports.show', $report->id)
            ->with('success', 'Report created successfully.');
    }

    /**
     * Display the specified report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $report = Report::with('latestResult')->findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Get the report results
        $results = ReportResult::where('report_id', $report->id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        
        return view('admin.reports.show', compact('report', 'results'));
    }

    /**
     * Show the form for editing the specified report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $report = Report::findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $types = Report::getTypes();
        
        return view('admin.reports.edit', compact('report', 'types'));
    }

    /**
     * Update the specified report in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(Report::getTypes())),
            'parameters' => 'nullable|array',
            'schedule' => 'nullable|array',
        ]);
        
        $report->update([
            'name' => $request->name,
            'description' => $request->description,
            'type' => $request->type,
            'parameters' => $request->parameters,
            'schedule' => $request->schedule,
            'is_favorite' => $request->has('is_favorite'),
        ]);
        
        return redirect()->route('admin.reports.show', $report->id)
            ->with('success', 'Report updated successfully.');
    }

    /**
     * Remove the specified report from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $report->delete();
        
        return redirect()->route('admin.reports.index')
            ->with('success', 'Report deleted successfully.');
    }

    /**
     * Generate the report.
     *
     * @param  int  $id
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate($id, Request $request)
    {
        $report = Report::findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Generate the report
        $result = $this->reportService->generateReport($report, $request->all());
        
        // Update the last generated at timestamp
        $report->update(['last_generated_at' => now()]);
        
        if ($result->isSuccessful()) {
            return redirect()->route('admin.reports.result', $result->id)
                ->with('success', 'Report generated successfully.');
        } else {
            return redirect()->route('admin.reports.show', $report->id)
                ->with('error', 'Failed to generate report: ' . $result->error_message);
        }
    }

    /**
     * Display the specified report result.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function result($id)
    {
        $result = ReportResult::with('report')->findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($result->report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        return view('admin.reports.result', compact('result'));
    }

    /**
     * Download the report result file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        $result = ReportResult::with('report')->findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($result->report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        // Check if the result has a file
        if (!$result->hasFile()) {
            return redirect()->route('admin.reports.result', $result->id)
                ->with('error', 'No file available for download.');
        }
        
        return Storage::download('public/' . $result->file_path);
    }

    /**
     * Toggle the favorite status of the report.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function toggleFavorite($id)
    {
        $report = Report::findOrFail($id);
        
        // Check if the report belongs to the authenticated user or user is admin
        if ($report->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        
        $report->update(['is_favorite' => !$report->is_favorite]);
        
        return redirect()->back()
            ->with('success', 'Report ' . ($report->is_favorite ? 'added to' : 'removed from') . ' favorites.');
    }
}

