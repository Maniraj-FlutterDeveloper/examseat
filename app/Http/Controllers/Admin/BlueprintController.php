<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blueprint;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\BloomsTaxonomy;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BlueprintController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Blueprint::with('subject');
        
        // Filter by subject if provided
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        $blueprints = $query->orderBy('id', 'desc')->paginate(10);
        $subjects = Subject::orderBy('subject_name')->get();
        
        return view('admin.blueprints.index', compact('blueprints', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $questionTypes = ['MCQ', 'Short Answer', 'Long Answer', 'True/False', 'Fill in the Blanks', 'Match the Following'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        $bloomsLevels = BloomsTaxonomy::where('is_active', true)->orderBy('order')->get();
        
        return view('admin.blueprints.create', compact(
            'subjects', 
            'questionTypes', 
            'difficultyLevels',
            'bloomsLevels'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'conditions' => 'required|array',
            'conditions.*.unit_id' => 'nullable|exists:units,id',
            'conditions.*.topic_id' => 'nullable|exists:topics,id',
            'conditions.*.bloom_id' => 'nullable|exists:blooms_taxonomy,id',
            'conditions.*.question_type' => 'nullable|string',
            'conditions.*.difficulty_level' => 'nullable|string',
            'conditions.*.marks' => 'required|numeric|min:0',
            'conditions.*.count' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $blueprint = Blueprint::create([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'description' => $request->description,
            'conditions' => $request->conditions,
            'total_marks' => $request->total_marks,
            'duration_minutes' => $request->duration_minutes,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.blueprints.index')
            ->with('success', 'Blueprint created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blueprint $blueprint)
    {
        $blueprint->load('subject');
        
        // Get all units, topics, and blooms taxonomy levels for reference
        $units = Unit::where('subject_id', $blueprint->subject_id)->get()->keyBy('id');
        $topics = Topic::whereIn('unit_id', $units->pluck('id'))->get()->keyBy('id');
        $bloomsLevels = BloomsTaxonomy::get()->keyBy('id');
        
        return view('admin.blueprints.show', compact('blueprint', 'units', 'topics', 'bloomsLevels'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blueprint $blueprint)
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $questionTypes = ['MCQ', 'Short Answer', 'Long Answer', 'True/False', 'Fill in the Blanks', 'Match the Following'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        $bloomsLevels = BloomsTaxonomy::where('is_active', true)->orderBy('order')->get();
        
        // Get units and topics for the subject
        $units = Unit::where('subject_id', $blueprint->subject_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
            
        $topics = Topic::whereIn('unit_id', $units->pluck('id'))
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        
        return view('admin.blueprints.edit', compact(
            'blueprint',
            'subjects', 
            'units',
            'topics',
            'questionTypes', 
            'difficultyLevels',
            'bloomsLevels'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blueprint $blueprint)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'conditions' => 'required|array',
            'conditions.*.unit_id' => 'nullable|exists:units,id',
            'conditions.*.topic_id' => 'nullable|exists:topics,id',
            'conditions.*.bloom_id' => 'nullable|exists:blooms_taxonomy,id',
            'conditions.*.question_type' => 'nullable|string',
            'conditions.*.difficulty_level' => 'nullable|string',
            'conditions.*.marks' => 'required|numeric|min:0',
            'conditions.*.count' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $blueprint->update([
            'subject_id' => $request->subject_id,
            'name' => $request->name,
            'description' => $request->description,
            'conditions' => $request->conditions,
            'total_marks' => $request->total_marks,
            'duration_minutes' => $request->duration_minutes,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.blueprints.index')
            ->with('success', 'Blueprint updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blueprint $blueprint)
    {
        // Check if blueprint has question papers
        if ($blueprint->questionPapers()->count() > 0) {
            return redirect()->route('admin.blueprints.index')
                ->with('error', 'Cannot delete blueprint because it has associated question papers.');
        }

        $blueprint->delete();

        return redirect()->route('admin.blueprints.index')
            ->with('success', 'Blueprint deleted successfully.');
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
    
    /**
     * Get topics for a specific unit (AJAX).
     */
    public function getTopicsByUnit(Request $request)
    {
        $unitId = $request->unit_id;
        $topics = Topic::where('unit_id', $unitId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id', 'topic_name']);
            
        return response()->json($topics);
    }
    
    /**
     * Generate a question paper from a blueprint.
     */
    public function generateQuestionPaper(Blueprint $blueprint)
    {
        // Load the subject
        $blueprint->load('subject');
        
        // Get all units, topics, and blooms taxonomy levels for reference
        $units = Unit::where('subject_id', $blueprint->subject_id)->get()->keyBy('id');
        $topics = Topic::whereIn('unit_id', $units->pluck('id'))->get()->keyBy('id');
        $bloomsLevels = BloomsTaxonomy::get()->keyBy('id');
        
        return view('admin.blueprints.generate_question_paper', compact('blueprint', 'units', 'topics', 'bloomsLevels'));
    }
}
