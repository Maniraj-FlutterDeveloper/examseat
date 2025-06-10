<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;
use App\Models\Blueprint;
use App\Models\Subject;
use App\Models\Unit;
use App\Models\Topic;
use App\Models\Question;
use App\Models\BloomsTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class QuestionPaperController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = QuestionPaper::with(['subject', 'blueprint']);
        
        // Filter by subject if provided
        if ($request->has('subject_id') && $request->subject_id) {
            $query->where('subject_id', $request->subject_id);
        }
        
        // Filter by blueprint if provided
        if ($request->has('blueprint_id') && $request->blueprint_id) {
            $query->where('blueprint_id', $request->blueprint_id);
        }
        
        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }
        
        $questionPapers = $query->orderBy('id', 'desc')->paginate(10);
        $subjects = Subject::orderBy('subject_name')->get();
        $blueprints = Blueprint::orderBy('name')->get();
        $statuses = ['draft', 'published', 'archived'];
        
        return view('admin.question_papers.index', compact('questionPapers', 'subjects', 'blueprints', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $blueprints = Blueprint::where('is_active', true)->orderBy('name')->get();
        $statuses = ['draft', 'published', 'archived'];
        
        return view('admin.question_papers.create', compact('subjects', 'blueprints', 'statuses'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'blueprint_id' => 'nullable|exists:blueprints,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array',
            'total_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'exam_date' => 'nullable|date',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $questionPaper = QuestionPaper::create([
            'subject_id' => $request->subject_id,
            'blueprint_id' => $request->blueprint_id,
            'title' => $request->title,
            'description' => $request->description,
            'questions' => $request->questions,
            'total_marks' => $request->total_marks,
            'duration_minutes' => $request->duration_minutes,
            'exam_date' => $request->exam_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.question-papers.index')
            ->with('success', 'Question Paper created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(QuestionPaper $questionPaper)
    {
        $questionPaper->load('subject', 'blueprint');
        
        // Get question details
        $questionIds = collect($questionPaper->questions)->pluck('id')->toArray();
        $questions = Question::whereIn('id', $questionIds)
            ->with(['topic', 'topic.unit', 'topic.unit.subject', 'bloomsTaxonomy'])
            ->get()
            ->keyBy('id');
        
        return view('admin.question_papers.show', compact('questionPaper', 'questions'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(QuestionPaper $questionPaper)
    {
        $questionPaper->load('subject', 'blueprint');
        
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $blueprints = Blueprint::where('is_active', true)->orderBy('name')->get();
        $statuses = ['draft', 'published', 'archived'];
        
        // Get question details
        $questionIds = collect($questionPaper->questions)->pluck('id')->toArray();
        $questions = Question::whereIn('id', $questionIds)
            ->with(['topic', 'topic.unit', 'topic.unit.subject', 'bloomsTaxonomy'])
            ->get()
            ->keyBy('id');
        
        return view('admin.question_papers.edit', compact('questionPaper', 'subjects', 'blueprints', 'statuses', 'questions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, QuestionPaper $questionPaper)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'blueprint_id' => 'nullable|exists:blueprints,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'questions' => 'required|array',
            'total_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'exam_date' => 'nullable|date',
            'status' => 'required|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $questionPaper->update([
            'subject_id' => $request->subject_id,
            'blueprint_id' => $request->blueprint_id,
            'title' => $request->title,
            'description' => $request->description,
            'questions' => $request->questions,
            'total_marks' => $request->total_marks,
            'duration_minutes' => $request->duration_minutes,
            'exam_date' => $request->exam_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.question-papers.index')
            ->with('success', 'Question Paper updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(QuestionPaper $questionPaper)
    {
        $questionPaper->delete();

        return redirect()->route('admin.question-papers.index')
            ->with('success', 'Question Paper deleted successfully.');
    }
    
    /**
     * Generate a random question paper.
     */
    public function generateRandom()
    {
        $subjects = Subject::where('is_active', true)->orderBy('subject_name')->get();
        $questionTypes = ['MCQ', 'Short Answer', 'Long Answer', 'True/False', 'Fill in the Blanks', 'Match the Following'];
        $difficultyLevels = ['easy', 'medium', 'hard'];
        $bloomsLevels = BloomsTaxonomy::where('is_active', true)->orderBy('order')->get();
        
        return view('admin.question_papers.generate_random', compact(
            'subjects', 
            'questionTypes', 
            'difficultyLevels',
            'bloomsLevels'
        ));
    }
    
    /**
     * Store a randomly generated question paper.
     */
    public function storeRandom(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'total_marks' => 'required|numeric|min:0',
            'duration_minutes' => 'required|integer|min:0',
            'exam_date' => 'nullable|date',
            'status' => 'required|in:draft,published,archived',
            'conditions' => 'required|array',
            'conditions.*.question_type' => 'nullable|string',
            'conditions.*.difficulty_level' => 'nullable|string',
            'conditions.*.bloom_id' => 'nullable|exists:blooms_taxonomy,id',
            'conditions.*.marks' => 'required|numeric|min:0',
            'conditions.*.count' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Get all questions for the subject
        $subjectId = $request->subject_id;
        $units = Unit::where('subject_id', $subjectId)->pluck('id');
        $topics = Topic::whereIn('unit_id', $units)->pluck('id');
        
        // Generate questions based on conditions
        $selectedQuestions = [];
        $totalMarks = 0;
        
        foreach ($request->conditions as $condition) {
            $query = Question::whereIn('topic_id', $topics);
            
            if (!empty($condition['question_type'])) {
                $query->where('question_type', $condition['question_type']);
            }
            
            if (!empty($condition['difficulty_level'])) {
                $query->where('difficulty_level', $condition['difficulty_level']);
            }
            
            if (!empty($condition['bloom_id'])) {
                $query->where('bloom_id', $condition['bloom_id']);
            }
            
            if (!empty($condition['marks'])) {
                $query->where('marks', $condition['marks']);
            }
            
            // Get random questions based on count
            $count = $condition['count'];
            $questions = $query->inRandomOrder()->limit($count)->get();
            
            foreach ($questions as $question) {
                $selectedQuestions[] = [
                    'id' => $question->id,
                    'marks' => $question->marks,
                    'question_type' => $question->question_type,
                    'section' => $condition['section'] ?? null,
                ];
                
                $totalMarks += $question->marks;
            }
        }
        
        // Create the question paper
        $questionPaper = QuestionPaper::create([
            'subject_id' => $request->subject_id,
            'blueprint_id' => null, // Random generation doesn't use a blueprint
            'title' => $request->title,
            'description' => $request->description,
            'questions' => $selectedQuestions,
            'total_marks' => $totalMarks,
            'duration_minutes' => $request->duration_minutes,
            'exam_date' => $request->exam_date,
            'status' => $request->status,
        ]);

        return redirect()->route('admin.question-papers.show', $questionPaper)
            ->with('success', 'Random Question Paper generated successfully.');
    }
    
    /**
     * Get blueprints for a specific subject (AJAX).
     */
    public function getBlueprintsBySubject(Request $request)
    {
        $subjectId = $request->subject_id;
        $blueprints = Blueprint::where('subject_id', $subjectId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        return response()->json($blueprints);
    }
    
    /**
     * Export question paper as PDF.
     */
    public function exportPdf(QuestionPaper $questionPaper)
    {
        $questionPaper->load('subject', 'blueprint');
        
        // Get question details
        $questionIds = collect($questionPaper->questions)->pluck('id')->toArray();
        $questions = Question::whereIn('id', $questionIds)
            ->with(['topic', 'topic.unit', 'topic.unit.subject', 'bloomsTaxonomy'])
            ->get()
            ->keyBy('id');
        
        // Generate PDF logic here
        // This would typically use a PDF library like DOMPDF, TCPDF, or Snappy
        
        return view('admin.question_papers.export_pdf', compact('questionPaper', 'questions'));
    }
}
