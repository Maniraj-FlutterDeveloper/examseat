<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Topic;
use App\Models\QuestionType;
use App\Models\BloomsTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questions = Question::with(['topic.unit.subject', 'questionType', 'bloomsTaxonomy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('question-bank.questions.index', compact('questions'));
    }

    /**
     * Show the form for creating a new question.
     *
     * @param  \App\Models\Topic|null  $topic
     * @return \Illuminate\Http\Response
     */
    public function create(Topic $topic = null)
    {
        $questionTypes = QuestionType::active()->orderBy('name')->get();
        $bloomsLevels = BloomsTaxonomy::active()->ordered()->get();
        
        // If topic is provided, get its unit and subject
        $unit = $topic ? $topic->unit : null;
        $subject = $unit ? $unit->subject : null;
        
        // If no topic is provided, we need to show a topic selector
        $topics = null;
        if (!$topic) {
            $topics = Topic::with('unit.subject')
                ->active()
                ->orderBy('name')
                ->get()
                ->groupBy(function ($topic) {
                    return $topic->unit->subject->name . ' > ' . $topic->unit->name;
                });
        }
        
        return view('question-bank.questions.create', compact(
            'topic',
            'unit',
            'subject',
            'topics',
            'questionTypes',
            'bloomsLevels'
        ));
    }

    /**
     * Store a newly created question in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'question_type_id' => 'required|exists:question_types,id',
            'blooms_taxonomy_id' => 'nullable|exists:blooms_taxonomy,id',
            'question_text' => 'required|string',
            'options' => 'nullable|array',
            'answer' => 'nullable|string',
            'solution' => 'nullable|string',
            'difficulty_level' => 'required|integer|min:1|max:5',
            'marks' => 'required|integer|min:1',
            'estimated_time' => 'nullable|integer|min:1',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // Set default estimated time if not provided (1 minute per mark)
        if (!isset($data['estimated_time']) || !$data['estimated_time']) {
            $data['estimated_time'] = $data['marks'] * 60; // Convert to seconds
        }
        
        $question = Question::create($data);
        
        // Get the topic, unit, and subject for redirection
        $topic = Topic::findOrFail($data['topic_id']);
        $unit = $topic->unit;
        $subject = $unit->subject;

        return redirect()->route('question-bank.questions.show', $question)
            ->with('success', 'Question created successfully.');
    }

    /**
     * Display the specified question.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function show(Question $question)
    {
        $question->load(['topic.unit.subject', 'questionType', 'bloomsTaxonomy']);
        
        return view('question-bank.questions.show', compact('question'));
    }

    /**
     * Show the form for editing the specified question.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(Question $question)
    {
        $question->load('topic.unit.subject');
        
        $questionTypes = QuestionType::active()->orderBy('name')->get();
        $bloomsLevels = BloomsTaxonomy::active()->ordered()->get();
        
        $topic = $question->topic;
        $unit = $topic->unit;
        $subject = $unit->subject;
        
        return view('question-bank.questions.edit', compact(
            'question',
            'topic',
            'unit',
            'subject',
            'questionTypes',
            'bloomsLevels'
        ));
    }

    /**
     * Update the specified question in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Question $question)
    {
        $validator = Validator::make($request->all(), [
            'topic_id' => 'required|exists:topics,id',
            'question_type_id' => 'required|exists:question_types,id',
            'blooms_taxonomy_id' => 'nullable|exists:blooms_taxonomy,id',
            'question_text' => 'required|string',
            'options' => 'nullable|array',
            'answer' => 'nullable|string',
            'solution' => 'nullable|string',
            'difficulty_level' => 'required|integer|min:1|max:5',
            'marks' => 'required|integer|min:1',
            'estimated_time' => 'nullable|integer|min:1',
            'metadata' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.questions.edit', $question)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // Set default estimated time if not provided (1 minute per mark)
        if (!isset($data['estimated_time']) || !$data['estimated_time']) {
            $data['estimated_time'] = $data['marks'] * 60; // Convert to seconds
        }
        
        $question->update($data);

        return redirect()->route('question-bank.questions.show', $question)
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function destroy(Question $question)
    {
        // Check if the question is used in any question papers
        if ($question->questionPapers()->count() > 0) {
            return redirect()->route('question-bank.questions.show', $question)
                ->with('error', 'Cannot delete question that is used in question papers.');
        }

        $question->delete();

        return redirect()->route('question-bank.questions.index')
            ->with('success', 'Question deleted successfully.');
    }

    /**
     * Toggle the active status of the specified question.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Question $question)
    {
        $question->is_active = !$question->is_active;
        $question->save();

        $status = $question->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Question {$status} successfully.");
    }

    /**
     * Search for questions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request)
    {
        $query = Question::with(['topic.unit.subject', 'questionType', 'bloomsTaxonomy']);
        
        // Apply filters
        if ($request->filled('subject_id')) {
            $query->whereHas('topic.unit', function ($q) use ($request) {
                $q->where('subject_id', $request->input('subject_id'));
            });
        }
        
        if ($request->filled('unit_id')) {
            $query->whereHas('topic', function ($q) use ($request) {
                $q->where('unit_id', $request->input('unit_id'));
            });
        }
        
        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->input('topic_id'));
        }
        
        if ($request->filled('question_type_id')) {
            $query->where('question_type_id', $request->input('question_type_id'));
        }
        
        if ($request->filled('blooms_taxonomy_id')) {
            $query->where('blooms_taxonomy_id', $request->input('blooms_taxonomy_id'));
        }
        
        if ($request->filled('difficulty_level')) {
            $query->where('difficulty_level', $request->input('difficulty_level'));
        }
        
        if ($request->filled('marks')) {
            $query->where('marks', $request->input('marks'));
        }
        
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('question_text', 'like', "%{$searchTerm}%")
                  ->orWhere('answer', 'like', "%{$searchTerm}%")
                  ->orWhere('solution', 'like', "%{$searchTerm}%");
            });
        }
        
        // Apply sorting
        $sortField = $request->input('sort', 'created_at');
        $sortDirection = $request->input('direction', 'desc');
        
        $allowedSortFields = [
            'created_at', 'difficulty_level', 'marks', 'estimated_time'
        ];
        
        if (in_array($sortField, $allowedSortFields)) {
            $query->orderBy($sortField, $sortDirection);
        }
        
        $questions = $query->paginate(10)->appends($request->all());
        
        // Get filter options for dropdowns
        $subjects = \App\Models\Subject::active()->orderBy('name')->get();
        $questionTypes = QuestionType::active()->orderBy('name')->get();
        $bloomsLevels = BloomsTaxonomy::active()->ordered()->get();
        
        return view('question-bank.questions.index', compact(
            'questions',
            'subjects',
            'questionTypes',
            'bloomsLevels'
        ));
    }

    /**
     * Clone the specified question.
     *
     * @param  \App\Models\Question  $question
     * @return \Illuminate\Http\Response
     */
    public function clone(Question $question)
    {
        $newQuestion = $question->replicate();
        $newQuestion->question_text = 'Copy of ' . $newQuestion->question_text;
        $newQuestion->save();

        return redirect()->route('question-bank.questions.edit', $newQuestion)
            ->with('success', 'Question cloned successfully. You can now edit the copy.');
    }
}

