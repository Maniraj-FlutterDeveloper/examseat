<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\Blueprint;
use App\Models\BlueprintCondition;
use App\Models\Subject;
use App\Models\QuestionType;
use App\Models\BloomsTaxonomy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class BlueprintController extends Controller
{
    /**
     * Display a listing of the blueprints.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blueprints = Blueprint::with('subject')
            ->withCount('conditions')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('question-bank.blueprints.index', compact('blueprints'));
    }

    /**
     * Show the form for creating a new blueprint.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $subjects = Subject::active()->orderBy('name')->get();
        
        return view('question-bank.blueprints.create', compact('subjects'));
    }

    /**
     * Store a newly created blueprint in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration' => 'required|integer|min:1',
            'structure' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.blueprints.create')
                ->withErrors($validator)
                ->withInput();
        }

        $blueprint = Blueprint::create($validator->validated());

        return redirect()->route('question-bank.blueprints.show', $blueprint)
            ->with('success', 'Blueprint created successfully.');
    }

    /**
     * Display the specified blueprint.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function show(Blueprint $blueprint)
    {
        $blueprint->load(['subject', 'conditions' => function ($query) {
            $query->with(['questionType', 'bloomsTaxonomy']);
        }]);
        
        // Group conditions by type
        $conditionsByType = $blueprint->conditions->groupBy('condition_type');
        
        // Calculate total marks
        $totalMarks = $blueprint->conditions->sum(function ($condition) {
            return $condition->question_count * $condition->marks_per_question;
        });
        
        // Calculate total questions
        $totalQuestions = $blueprint->conditions->sum('question_count');
        
        return view('question-bank.blueprints.show', compact(
            'blueprint',
            'conditionsByType',
            'totalMarks',
            'totalQuestions'
        ));
    }

    /**
     * Show the form for editing the specified blueprint.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function edit(Blueprint $blueprint)
    {
        $subjects = Subject::active()->orderBy('name')->get();
        
        return view('question-bank.blueprints.edit', compact('blueprint', 'subjects'));
    }

    /**
     * Update the specified blueprint in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Blueprint $blueprint)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject_id' => 'required|exists:subjects,id',
            'duration' => 'required|integer|min:1',
            'structure' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.blueprints.edit', $blueprint)
                ->withErrors($validator)
                ->withInput();
        }

        $blueprint->update($validator->validated());

        return redirect()->route('question-bank.blueprints.show', $blueprint)
            ->with('success', 'Blueprint updated successfully.');
    }

    /**
     * Remove the specified blueprint from storage.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function destroy(Blueprint $blueprint)
    {
        // Check if the blueprint is used in any question papers
        if ($blueprint->questionPapers()->count() > 0) {
            return redirect()->route('question-bank.blueprints.show', $blueprint)
                ->with('error', 'Cannot delete blueprint that is used in question papers.');
        }

        // Delete all conditions first
        $blueprint->conditions()->delete();
        
        // Then delete the blueprint
        $blueprint->delete();

        return redirect()->route('question-bank.blueprints.index')
            ->with('success', 'Blueprint deleted successfully.');
    }

    /**
     * Toggle the active status of the specified blueprint.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Blueprint $blueprint)
    {
        $blueprint->is_active = !$blueprint->is_active;
        $blueprint->save();

        $status = $blueprint->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Blueprint {$status} successfully.");
    }

    /**
     * Show the form for adding a condition to the blueprint.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function createCondition(Blueprint $blueprint)
    {
        $subject = $blueprint->subject;
        
        // Get units and topics for the subject
        $units = $subject->units()->with('topics')->active()->ordered()->get();
        
        $questionTypes = QuestionType::active()->orderBy('name')->get();
        $bloomsLevels = BloomsTaxonomy::active()->ordered()->get();
        
        $conditionTypes = [
            'subject' => 'Entire Subject',
            'unit' => 'Specific Unit',
            'topic' => 'Specific Topic',
        ];
        
        return view('question-bank.blueprints.create-condition', compact(
            'blueprint',
            'subject',
            'units',
            'questionTypes',
            'bloomsLevels',
            'conditionTypes'
        ));
    }

    /**
     * Store a newly created condition in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function storeCondition(Request $request, Blueprint $blueprint)
    {
        $validator = Validator::make($request->all(), [
            'condition_type' => 'required|string|in:subject,unit,topic',
            'reference_id' => 'nullable|integer',
            'question_count' => 'required|integer|min:1',
            'marks_per_question' => 'required|integer|min:1',
            'question_type_id' => 'nullable|exists:question_types,id',
            'blooms_taxonomy_id' => 'nullable|exists:blooms_taxonomy,id',
            'difficulty_level' => 'nullable|integer|min:1|max:5',
            'additional_criteria' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.blueprints.create-condition', $blueprint)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // Validate reference_id based on condition_type
        if ($data['condition_type'] === 'unit') {
            $unit = \App\Models\Unit::find($data['reference_id']);
            if (!$unit || $unit->subject_id !== $blueprint->subject_id) {
                return redirect()->route('question-bank.blueprints.create-condition', $blueprint)
                    ->with('error', 'Invalid unit selected.')
                    ->withInput();
            }
        } elseif ($data['condition_type'] === 'topic') {
            $topic = \App\Models\Topic::find($data['reference_id']);
            if (!$topic || $topic->unit->subject_id !== $blueprint->subject_id) {
                return redirect()->route('question-bank.blueprints.create-condition', $blueprint)
                    ->with('error', 'Invalid topic selected.')
                    ->withInput();
            }
        } else {
            // For subject type, reference_id is not needed
            $data['reference_id'] = null;
        }
        
        // Create the condition
        $condition = $blueprint->conditions()->create($data);
        
        // Update the total marks of the blueprint
        $blueprint->updateTotalMarks();

        return redirect()->route('question-bank.blueprints.show', $blueprint)
            ->with('success', 'Condition added successfully.');
    }

    /**
     * Show the form for editing a condition.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @param  \App\Models\BlueprintCondition  $condition
     * @return \Illuminate\Http\Response
     */
    public function editCondition(Blueprint $blueprint, BlueprintCondition $condition)
    {
        // Ensure the condition belongs to the blueprint
        if ($condition->blueprint_id !== $blueprint->id) {
            abort(404);
        }
        
        $subject = $blueprint->subject;
        
        // Get units and topics for the subject
        $units = $subject->units()->with('topics')->active()->ordered()->get();
        
        $questionTypes = QuestionType::active()->orderBy('name')->get();
        $bloomsLevels = BloomsTaxonomy::active()->ordered()->get();
        
        $conditionTypes = [
            'subject' => 'Entire Subject',
            'unit' => 'Specific Unit',
            'topic' => 'Specific Topic',
        ];
        
        return view('question-bank.blueprints.edit-condition', compact(
            'blueprint',
            'condition',
            'subject',
            'units',
            'questionTypes',
            'bloomsLevels',
            'conditionTypes'
        ));
    }

    /**
     * Update the specified condition in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Blueprint  $blueprint
     * @param  \App\Models\BlueprintCondition  $condition
     * @return \Illuminate\Http\Response
     */
    public function updateCondition(Request $request, Blueprint $blueprint, BlueprintCondition $condition)
    {
        // Ensure the condition belongs to the blueprint
        if ($condition->blueprint_id !== $blueprint->id) {
            abort(404);
        }
        
        $validator = Validator::make($request->all(), [
            'condition_type' => 'required|string|in:subject,unit,topic',
            'reference_id' => 'nullable|integer',
            'question_count' => 'required|integer|min:1',
            'marks_per_question' => 'required|integer|min:1',
            'question_type_id' => 'nullable|exists:question_types,id',
            'blooms_taxonomy_id' => 'nullable|exists:blooms_taxonomy,id',
            'difficulty_level' => 'nullable|integer|min:1|max:5',
            'additional_criteria' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.blueprints.edit-condition', [$blueprint, $condition])
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // Validate reference_id based on condition_type
        if ($data['condition_type'] === 'unit') {
            $unit = \App\Models\Unit::find($data['reference_id']);
            if (!$unit || $unit->subject_id !== $blueprint->subject_id) {
                return redirect()->route('question-bank.blueprints.edit-condition', [$blueprint, $condition])
                    ->with('error', 'Invalid unit selected.')
                    ->withInput();
            }
        } elseif ($data['condition_type'] === 'topic') {
            $topic = \App\Models\Topic::find($data['reference_id']);
            if (!$topic || $topic->unit->subject_id !== $blueprint->subject_id) {
                return redirect()->route('question-bank.blueprints.edit-condition', [$blueprint, $condition])
                    ->with('error', 'Invalid topic selected.')
                    ->withInput();
            }
        } else {
            // For subject type, reference_id is not needed
            $data['reference_id'] = null;
        }
        
        // Update the condition
        $condition->update($data);
        
        // Update the total marks of the blueprint
        $blueprint->updateTotalMarks();

        return redirect()->route('question-bank.blueprints.show', $blueprint)
            ->with('success', 'Condition updated successfully.');
    }

    /**
     * Remove the specified condition from storage.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @param  \App\Models\BlueprintCondition  $condition
     * @return \Illuminate\Http\Response
     */
    public function destroyCondition(Blueprint $blueprint, BlueprintCondition $condition)
    {
        // Ensure the condition belongs to the blueprint
        if ($condition->blueprint_id !== $blueprint->id) {
            abort(404);
        }
        
        $condition->delete();
        
        // Update the total marks of the blueprint
        $blueprint->updateTotalMarks();

        return redirect()->route('question-bank.blueprints.show', $blueprint)
            ->with('success', 'Condition deleted successfully.');
    }

    /**
     * Preview the questions that match the blueprint.
     *
     * @param  \App\Models\Blueprint  $blueprint
     * @return \Illuminate\Http\Response
     */
    public function previewQuestions(Blueprint $blueprint)
    {
        $blueprint->load(['subject', 'conditions' => function ($query) {
            $query->with(['questionType', 'bloomsTaxonomy']);
        }]);
        
        // Get matching questions for each condition
        $conditionsWithQuestions = [];
        
        foreach ($blueprint->conditions as $condition) {
            $matchingQuestions = $condition->getMatchingQuestions();
            
            $conditionsWithQuestions[] = [
                'condition' => $condition,
                'questions' => $matchingQuestions,
                'available_count' => $matchingQuestions->count(),
                'required_count' => $condition->question_count,
                'sufficient' => $matchingQuestions->count() >= $condition->question_count,
            ];
        }
        
        // Check if there are enough questions for all conditions
        $allSufficient = collect($conditionsWithQuestions)->every(function ($item) {
            return $item['sufficient'];
        });
        
        return view('question-bank.blueprints.preview-questions', compact(
            'blueprint',
            'conditionsWithQuestions',
            'allSufficient'
        ));
    }
}

