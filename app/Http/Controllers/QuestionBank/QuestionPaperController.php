<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;
use App\Models\Blueprint;
use App\Models\Subject;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class QuestionPaperController extends Controller
{
    /**
     * Display a listing of the question papers.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questionPapers = QuestionPaper::with(['subject', 'blueprint'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('question-bank.question-papers.index', compact('questionPapers'));
    }

    /**
     * Show the form for creating a new question paper.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $blueprints = Blueprint::active()->orderBy('name')->get();
        
        return view('question-bank.question-papers.create', compact('subjects', 'blueprints'));
    }

    /**
     * Store a newly created question paper in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'blueprint_id' => 'nullable|exists:blueprints,id',
            'total_marks' => 'nullable|integer|min:1',
            'duration' => 'required|integer|min:1',
            'exam_date' => 'nullable|date',
            'status' => 'required|string|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.question-papers.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // If blueprint is provided, get its total marks and duration
        if (!empty($data['blueprint_id'])) {
            $blueprint = Blueprint::findOrFail($data['blueprint_id']);
            
            // Ensure the blueprint belongs to the selected subject
            if ($blueprint->subject_id != $data['subject_id']) {
                return redirect()->route('question-bank.question-papers.create')
                    ->with('error', 'The selected blueprint does not belong to the selected subject.')
                    ->withInput();
            }
            
            // Set total marks from blueprint if not provided
            if (empty($data['total_marks'])) {
                $data['total_marks'] = $blueprint->total_marks;
            }
            
            // Set duration from blueprint if not provided
            if (empty($data['duration'])) {
                $data['duration'] = $blueprint->duration;
            }
        }
        
        $questionPaper = QuestionPaper::create($data);

        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('success', 'Question paper created successfully.');
    }

    /**
     * Display the specified question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionPaper $questionPaper)
    {
        $questionPaper->load(['subject', 'blueprint']);
        
        // Get questions grouped by section
        $sections = $questionPaper->getQuestionsBySection();
        
        return view('question-bank.question-papers.show', compact('questionPaper', 'sections'));
    }

    /**
     * Show the form for editing the specified question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function edit(QuestionPaper $questionPaper)
    {
        $subjects = Subject::active()->orderBy('name')->get();
        $blueprints = Blueprint::active()->orderBy('name')->get();
        
        return view('question-bank.question-papers.edit', compact('questionPaper', 'subjects', 'blueprints'));
    }

    /**
     * Update the specified question paper in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuestionPaper $questionPaper)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'blueprint_id' => 'nullable|exists:blueprints,id',
            'total_marks' => 'nullable|integer|min:1',
            'duration' => 'required|integer|min:1',
            'exam_date' => 'nullable|date',
            'status' => 'required|string|in:draft,published,archived',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.question-papers.edit', $questionPaper)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // If blueprint is provided, get its total marks and duration
        if (!empty($data['blueprint_id'])) {
            $blueprint = Blueprint::findOrFail($data['blueprint_id']);
            
            // Ensure the blueprint belongs to the selected subject
            if ($blueprint->subject_id != $data['subject_id']) {
                return redirect()->route('question-bank.question-papers.edit', $questionPaper)
                    ->with('error', 'The selected blueprint does not belong to the selected subject.')
                    ->withInput();
            }
        }
        
        $questionPaper->update($data);

        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('success', 'Question paper updated successfully.');
    }

    /**
     * Remove the specified question paper from storage.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuestionPaper $questionPaper)
    {
        // Delete the question paper questions first
        $questionPaper->questions()->detach();
        
        // Then delete the question paper
        $questionPaper->delete();

        return redirect()->route('question-bank.question-papers.index')
            ->with('success', 'Question paper deleted successfully.');
    }

    /**
     * Generate a question paper from a blueprint.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function generate(QuestionPaper $questionPaper)
    {
        // Check if the question paper has a blueprint
        if (!$questionPaper->blueprint_id) {
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('error', 'This question paper does not have a blueprint.');
        }
        
        // Check if the question paper already has questions
        if ($questionPaper->questions()->count() > 0) {
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('error', 'This question paper already has questions. Please clear it first.');
        }
        
        $blueprint = $questionPaper->blueprint;
        
        // Get all conditions from the blueprint
        $conditions = $blueprint->conditions()->with(['questionType', 'bloomsTaxonomy'])->get();
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            $sectionNumber = 1;
            $questionNumber = 1;
            
            foreach ($conditions as $condition) {
                // Get matching questions for this condition
                $matchingQuestions = $condition->getMatchingQuestions();
                
                // Check if there are enough questions
                if ($matchingQuestions->count() < $condition->question_count) {
                    throw new \Exception("Not enough questions for condition: {$condition->id}");
                }
                
                // Randomly select the required number of questions
                $selectedQuestions = $matchingQuestions->random($condition->question_count);
                
                // Add the questions to the question paper
                foreach ($selectedQuestions as $question) {
                    $questionPaper->questions()->attach($question->id, [
                        'section_number' => $sectionNumber,
                        'question_number' => $questionNumber++,
                        'marks' => $condition->marks_per_question,
                        'is_optional' => false,
                    ]);
                }
                
                // Increment section number for the next condition
                $sectionNumber++;
                $questionNumber = 1;
            }
            
            // Update the total marks of the question paper
            $questionPaper->updateTotalMarks();
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('success', 'Question paper generated successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('error', 'Failed to generate question paper: ' . $e->getMessage());
        }
    }

    /**
     * Generate a random question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function generateRandom(QuestionPaper $questionPaper)
    {
        // Check if the question paper already has questions
        if ($questionPaper->questions()->count() > 0) {
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('error', 'This question paper already has questions. Please clear it first.');
        }
        
        // Get the subject
        $subject = $questionPaper->subject;
        
        // Get all active questions for the subject
        $questions = Question::whereHas('topic.unit', function ($query) use ($subject) {
            $query->where('subject_id', $subject->id);
        })->active()->get();
        
        // Check if there are enough questions
        if ($questions->count() < 10) {
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('error', 'Not enough questions available for this subject.');
        }
        
        // Start a database transaction
        DB::beginTransaction();
        
        try {
            // Randomly select questions
            $selectedQuestions = $questions->random(min(10, $questions->count()));
            
            // Add the questions to the question paper
            foreach ($selectedQuestions as $index => $question) {
                $questionPaper->questions()->attach($question->id, [
                    'section_number' => 1,
                    'question_number' => $index + 1,
                    'marks' => $question->marks,
                    'is_optional' => false,
                ]);
            }
            
            // Update the total marks of the question paper
            $questionPaper->updateTotalMarks();
            
            // Commit the transaction
            DB::commit();
            
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('success', 'Random question paper generated successfully.');
        } catch (\Exception $e) {
            // Rollback the transaction
            DB::rollBack();
            
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('error', 'Failed to generate random question paper: ' . $e->getMessage());
        }
    }

    /**
     * Clear all questions from the question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function clear(QuestionPaper $questionPaper)
    {
        // Delete all questions from the question paper
        $questionPaper->questions()->detach();
        
        // Update the total marks
        $questionPaper->total_marks = 0;
        $questionPaper->save();

        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('success', 'Question paper cleared successfully.');
    }

    /**
     * Show the form for adding a question to the question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function addQuestion(QuestionPaper $questionPaper)
    {
        $subject = $questionPaper->subject;
        
        // Get all active questions for the subject
        $questions = Question::whereHas('topic.unit', function ($query) use ($subject) {
            $query->where('subject_id', $subject->id);
        })->with(['topic.unit', 'questionType', 'bloomsTaxonomy'])
          ->active()
          ->paginate(10);
        
        // Get the maximum section and question numbers
        $maxSection = $questionPaper->questions()->max('pivot_section_number') ?? 0;
        $maxQuestion = $questionPaper->questions()->max('pivot_question_number') ?? 0;
        
        return view('question-bank.question-papers.add-question', compact(
            'questionPaper',
            'subject',
            'questions',
            'maxSection',
            'maxQuestion'
        ));
    }

    /**
     * Store a question in the question paper.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function storeQuestion(Request $request, QuestionPaper $questionPaper)
    {
        $validator = Validator::make($request->all(), [
            'question_id' => 'required|exists:questions,id',
            'section_number' => 'required|integer|min:1',
            'question_number' => 'required|integer|min:1',
            'marks' => 'required|integer|min:1',
            'is_optional' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.question-papers.add-question', $questionPaper)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // Check if the question belongs to the subject
        $question = Question::findOrFail($data['question_id']);
        $subject = $questionPaper->subject;
        
        if ($question->topic->unit->subject_id !== $subject->id) {
            return redirect()->route('question-bank.question-papers.add-question', $questionPaper)
                ->with('error', 'The selected question does not belong to the subject of this question paper.')
                ->withInput();
        }
        
        // Check if the question is already in the question paper
        if ($questionPaper->questions()->where('question_id', $data['question_id'])->exists()) {
            return redirect()->route('question-bank.question-papers.add-question', $questionPaper)
                ->with('error', 'This question is already in the question paper.')
                ->withInput();
        }
        
        // Add the question to the question paper
        $questionPaper->questions()->attach($data['question_id'], [
            'section_number' => $data['section_number'],
            'question_number' => $data['question_number'],
            'marks' => $data['marks'],
            'is_optional' => $data['is_optional'] ?? false,
        ]);
        
        // Update the total marks of the question paper
        $questionPaper->updateTotalMarks();

        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('success', 'Question added to the question paper successfully.');
    }

    /**
     * Remove a question from the question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @param  int  $questionId
     * @return \Illuminate\Http\Response
     */
    public function removeQuestion(QuestionPaper $questionPaper, $questionId)
    {
        // Remove the question from the question paper
        $questionPaper->questions()->detach($questionId);
        
        // Update the total marks of the question paper
        $questionPaper->updateTotalMarks();

        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('success', 'Question removed from the question paper successfully.');
    }

    /**
     * Export the question paper as PDF.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function exportPdf(QuestionPaper $questionPaper)
    {
        // This would be implemented with a PDF generation library
        // For now, we'll just redirect back with a message
        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('info', 'PDF export functionality will be implemented soon.');
    }

    /**
     * Publish the question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function publish(QuestionPaper $questionPaper)
    {
        // Check if the question paper has questions
        if ($questionPaper->questions()->count() === 0) {
            return redirect()->route('question-bank.question-papers.show', $questionPaper)
                ->with('error', 'Cannot publish an empty question paper.');
        }
        
        $questionPaper->status = 'published';
        $questionPaper->save();

        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('success', 'Question paper published successfully.');
    }

    /**
     * Archive the question paper.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @return \Illuminate\Http\Response
     */
    public function archive(QuestionPaper $questionPaper)
    {
        $questionPaper->status = 'archived';
        $questionPaper->save();

        return redirect()->route('question-bank.question-papers.show', $questionPaper)
            ->with('success', 'Question paper archived successfully.');
    }
}

