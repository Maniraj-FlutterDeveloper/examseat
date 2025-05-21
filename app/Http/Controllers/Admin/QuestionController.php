<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Topic;
use App\Models\Unit;
use App\Models\Subject;
use App\Models\BloomsTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Question::with(['topic', 'topic.unit', 'topic.unit.subject', 'bloomsTaxonomy']);
        
        // Filter by topic if provided
        if ($request->has('topic_id') && $request->topic_id) {
            $query->where('topic_id', $request->topic_id);
        }
        
        // Filter by unit if provided
        if ($request->has('unit_id') && $request->unit_id) {
            $query->whereHas('topic', function($q) use ($request) {
                $q->where('unit_id', $request->unit_id);
            });
        }
        
        // Filter by subject if provided
        if ($request->has('subject_id') && $request->subject_id) {
            $query->whereHas('topic.unit', function($q) use ($request) {
                $q->where('subject_id', $request->subject_id);
            });
        }
        
        // Filter by Bloom's Taxonomy level if provided
        if ($request->has('bloom_id') && $request->bloom_id) {
            $query->where('bloom_id', $request->bloom_id);
        }
        
        // Filter by question type if provided
        if ($request->has('question_type') && $request->question_type) {
            $query->where('question_type', $request->question_type);
        }
        
        // Filter by difficulty level if provided
        if ($request->has('difficulty_level') && $request->difficulty_level) {
            $query->where('difficulty_level', $request->difficulty_level);
        }
        
        $questions = $query->orderBy('id', 'desc')->paginate(10);
        $subjects = Subject::orderBy('subject_name')->get();
        $units = Unit::orderBy('unit_name')->get();
        $topics = Topic::orderBy('topic_name')->get();
        $bloomsLevels = BloomsTaxonomy::orderBy('order')->get();
        $questionTypes = ['MCQ', 'Short Answer', 'Long Answer', 'True/False', 'Fill in the Blanks', 'Match the Following'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        return view('admin.questions.index', compact(
            'questions', 
            'subjects', 
            'units', 
            'topics', 
            'bloomsLevels', 
            'questionTypes', 
            'difficultyLevels'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $bloomsLevels = BloomsTaxonomy::where('is_active', true)->orderBy('order')->get();
        $questionTypes = ['MCQ', 'Short Answer', 'Long Answer', 'True/False', 'Fill in the Blanks', 'Match the Following'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        // Initialize empty collections
        $units = collect();
        $topics = collect();
        
        // If subject_id is provided, get units for that subject
        if ($request->has('subject_id') && $request->subject_id) {
            $units = Unit::where('subject_id', $request->subject_id)
                ->where('is_active', true)
                ->orderBy('order')
                ->get();
                
            // If unit_id is provided, get topics for that unit
            if ($request->has('unit_id') && $request->unit_id) {
                $topics = Topic::where('unit_id', $request->unit_id)
                    ->where('is_active', true)
                    ->orderBy('order')
                    ->get();
            }
        }
        
        return view('admin.questions.create', compact(
            'subjects', 
            'units', 
            'topics', 
            'bloomsLevels', 
            'questionTypes', 
            'difficultyLevels'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'bloom_id' => 'required|exists:blooms_taxonomy,id',
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:MCQ,Short Answer,Long Answer,True/False,Fill in the Blanks,Match the Following',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'marks' => 'required|numeric|min:0',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process options based on question type
        $options = null;
        if ($request->question_type === 'MCQ' && $request->has('options')) {
            $options = $request->options;
        } elseif ($request->question_type === 'Match the Following' && $request->has('match_left') && $request->has('match_right')) {
            $options = [
                'left' => $request->match_left,
                'right' => $request->match_right
            ];
        }

        $question = Question::create([
            'topic_id' => $request->topic_id,
            'bloom_id' => $request->bloom_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'marks' => $request->marks,
            'difficulty_level' => $request->difficulty_level,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Question $question)
    {
        $question->load('topic', 'topic.unit', 'topic.unit.subject', 'bloomsTaxonomy');
        return view('admin.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Question $question)
    {
        $question->load('topic', 'topic.unit', 'topic.unit.subject');
        
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $units = Unit::where('subject_id', $question->topic->unit->subject_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        $topics = Topic::where('unit_id', $question->topic->unit_id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        $bloomsLevels = BloomsTaxonomy::where('is_active', true)->orderBy('order')->get();
        $questionTypes = ['MCQ', 'Short Answer', 'Long Answer', 'True/False', 'Fill in the Blanks', 'Match the Following'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        
        return view('admin.questions.edit', compact(
            'question',
            'subjects', 
            'units', 
            'topics', 
            'bloomsLevels', 
            'questionTypes', 
            'difficultyLevels'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'bloom_id' => 'required|exists:blooms_taxonomy,id',
            'question_text' => 'required|string',
            'question_type' => 'required|string|in:MCQ,Short Answer,Long Answer,True/False,Fill in the Blanks,Match the Following',
            'options' => 'nullable|array',
            'correct_answer' => 'nullable|string',
            'marks' => 'required|numeric|min:0',
            'difficulty_level' => 'required|in:easy,medium,hard',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Process options based on question type
        $options = null;
        if ($request->question_type === 'MCQ' && $request->has('options')) {
            $options = $request->options;
        } elseif ($request->question_type === 'Match the Following' && $request->has('match_left') && $request->has('match_right')) {
            $options = [
                'left' => $request->match_left,
                'right' => $request->match_right
            ];
        }

        $question->update([
            'topic_id' => $request->topic_id,
            'bloom_id' => $request->bloom_id,
            'question_text' => $request->question_text,
            'question_type' => $request->question_type,
            'options' => $options,
            'correct_answer' => $request->correct_answer,
            'marks' => $request->marks,
            'difficulty_level' => $request->difficulty_level,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Question $question)
    {
        $question->delete();

        return redirect()->route('admin.questions.index')
            ->with('success', 'Question deleted successfully.');
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
}
