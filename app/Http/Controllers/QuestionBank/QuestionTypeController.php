<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\QuestionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class QuestionTypeController extends Controller
{
    /**
     * Display a listing of the question types.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $questionTypes = QuestionType::withCount('questions')
            ->orderBy('name')
            ->get();
            
        return view('question-bank.question-types.index', compact('questionTypes'));
    }

    /**
     * Show the form for creating a new question type.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('question-bank.question-types.create');
    }

    /**
     * Store a newly created question type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:question_types',
            'description' => 'nullable|string',
            'structure' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.question-types.create')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // Handle structure JSON
        if (isset($data['structure']) && is_array($data['structure'])) {
            // Ensure the structure is properly formatted
            $data['structure'] = $this->validateAndFormatStructure($data['structure']);
        }

        $questionType = QuestionType::create($data);

        return redirect()->route('question-bank.question-types.index')
            ->with('success', 'Question type created successfully.');
    }

    /**
     * Display the specified question type.
     *
     * @param  \App\Models\QuestionType  $questionType
     * @return \Illuminate\Http\Response
     */
    public function show(QuestionType $questionType)
    {
        $questionType->loadCount('questions');
        
        $questions = $questionType->questions()
            ->with(['topic.unit.subject', 'bloomsTaxonomy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('question-bank.question-types.show', compact('questionType', 'questions'));
    }

    /**
     * Show the form for editing the specified question type.
     *
     * @param  \App\Models\QuestionType  $questionType
     * @return \Illuminate\Http\Response
     */
    public function edit(QuestionType $questionType)
    {
        return view('question-bank.question-types.edit', compact('questionType'));
    }

    /**
     * Update the specified question type in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\QuestionType  $questionType
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, QuestionType $questionType)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('question_types')->ignore($questionType->id),
            ],
            'description' => 'nullable|string',
            'structure' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.question-types.edit', $questionType)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // Handle structure JSON
        if (isset($data['structure']) && is_array($data['structure'])) {
            // Ensure the structure is properly formatted
            $data['structure'] = $this->validateAndFormatStructure($data['structure']);
        }

        $questionType->update($data);

        return redirect()->route('question-bank.question-types.index')
            ->with('success', 'Question type updated successfully.');
    }

    /**
     * Remove the specified question type from storage.
     *
     * @param  \App\Models\QuestionType  $questionType
     * @return \Illuminate\Http\Response
     */
    public function destroy(QuestionType $questionType)
    {
        // Check if the question type has associated questions
        if ($questionType->questions()->count() > 0) {
            return redirect()->route('question-bank.question-types.index')
                ->with('error', 'Cannot delete question type with associated questions.');
        }

        $questionType->delete();

        return redirect()->route('question-bank.question-types.index')
            ->with('success', 'Question type deleted successfully.');
    }

    /**
     * Toggle the active status of the specified question type.
     *
     * @param  \App\Models\QuestionType  $questionType
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(QuestionType $questionType)
    {
        $questionType->is_active = !$questionType->is_active;
        $questionType->save();

        $status = $questionType->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Question type {$status} successfully.");
    }

    /**
     * Validate and format the structure array.
     *
     * @param  array  $structure
     * @return array
     */
    private function validateAndFormatStructure(array $structure)
    {
        $formattedStructure = [];
        
        // Ensure each field has the required properties
        foreach ($structure as $field) {
            if (isset($field['name']) && isset($field['type'])) {
                $formattedField = [
                    'name' => $field['name'],
                    'type' => $field['type'],
                    'required' => $field['required'] ?? false,
                    'label' => $field['label'] ?? $field['name'],
                ];
                
                // Add additional properties based on field type
                switch ($field['type']) {
                    case 'select':
                    case 'radio':
                    case 'checkbox':
                        $formattedField['options'] = $field['options'] ?? [];
                        break;
                    case 'number':
                        $formattedField['min'] = $field['min'] ?? null;
                        $formattedField['max'] = $field['max'] ?? null;
                        break;
                    case 'text':
                    case 'textarea':
                        $formattedField['placeholder'] = $field['placeholder'] ?? '';
                        $formattedField['maxlength'] = $field['maxlength'] ?? null;
                        break;
                }
                
                $formattedStructure[] = $formattedField;
            }
        }
        
        return $formattedStructure;
    }
}

