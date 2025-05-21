<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use App\Models\Unit;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TopicController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Topic::with(['unit', 'unit.subject']);
        
        // Filter by unit if provided
        if ($request->has('unit_id') && $request->unit_id) {
            $query->where('unit_id', $request->unit_id);
        }
        
        // Filter by subject if provided
        if ($request->has('subject_id') && $request->subject_id) {
            $query->whereHas('unit', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }
        
        $topics = $query->orderBy('unit_id')->orderBy('order')->paginate(10);
        $subjects = Subject::orderBy('subject_name')->get();
        $units = Unit::orderBy('unit_name')->get();
        
        return view('admin.topics.index', compact('topics', 'subjects', 'units'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        
        // If subject_id is provided, get units for that subject
        $units = collect();
        if ($request->has('subject_id') && $request->subject_id) {
            $units = Unit::where('subject_id', $request->subject_id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
        }
        
        return view('admin.topics.create', compact('subjects', 'units'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required|exists:units,id',
            'topic_name' => 'required|string|max:255',
            'topic_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $topic = Topic::create([
            'unit_id' => $request->unit_id,
            'topic_name' => $request->topic_name,
            'topic_code' => $request->topic_code,
            'description' => $request->description,
            'order' => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Topic $topic)
    {
        $topic->load('unit', 'unit.subject', 'questions');
        return view('admin.topics.show', compact('topic'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Topic $topic)
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $units = Unit::where('subject_id', $topic->unit->subject_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
            
        return view('admin.topics.edit', compact('topic', 'subjects', 'units'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Topic $topic)
    {
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required|exists:units,id',
            'topic_name' => 'required|string|max:255',
            'topic_code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $topic->update([
            'unit_id' => $request->unit_id,
            'topic_name' => $request->topic_name,
            'topic_code' => $request->topic_code,
            'description' => $request->description,
            'order' => $request->order ?? $topic->order,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Topic $topic)
    {
        // Check if topic has questions
        if ($topic->questions()->count() > 0) {
            return redirect()->route('admin.topics.index')
                ->with('error', 'Cannot delete topic because it has associated questions.');
        }

        $topic->delete();

        return redirect()->route('admin.topics.index')
            ->with('success', 'Topic deleted successfully.');
    }
    
    /**
     * Get units for a specific subject (AJAX).
     */
    public function getUnitsBySubject(Request $request)
    {
        $subjectId = $request->subject_id;
        $units = Unit::where('subject_id', $subjectId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id', 'unit_name']);
            
        return response()->json($units);
    }
}
