<?php

namespace App\Http\Controllers\QuestionBank;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TopicController extends Controller
{
    /**
     * Display a listing of the topics.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function index(Unit $unit)
    {
        $subject = $unit->subject;
        
        $topics = $unit->topics()
            ->withCount('questions')
            ->orderBy('order')
            ->paginate(10);
            
        return view('question-bank.topics.index', compact('subject', 'unit', 'topics'));
    }

    /**
     * Show the form for creating a new topic.
     *
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function create(Unit $unit)
    {
        $subject = $unit->subject;
        
        // Get the highest order value to set the default for the new topic
        $maxOrder = $unit->topics()->max('order') ?? 0;
        
        return view('question-bank.topics.create', compact('subject', 'unit', 'maxOrder'));
    }

    /**
     * Store a newly created topic in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics')->where(function ($query) use ($unit) {
                    return $query->where('unit_id', $unit->id);
                }),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('topics')->where(function ($query) use ($unit) {
                    return $query->where('unit_id', $unit->id);
                }),
            ],
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.units.topics.create', $unit)
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        // If order is not provided, set it to the highest order + 1
        if (!isset($data['order'])) {
            $data['order'] = $unit->topics()->max('order') + 1;
        }
        
        $topic = $unit->topics()->create($data);
        
        $subject = $unit->subject;

        return redirect()->route('question-bank.units.topics.show', [$unit, $topic])
            ->with('success', 'Topic created successfully.');
    }

    /**
     * Display the specified topic.
     *
     * @param  \App\Models\Unit  $unit
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function show(Unit $unit, Topic $topic)
    {
        // Ensure the topic belongs to the unit
        if ($topic->unit_id !== $unit->id) {
            abort(404);
        }
        
        $subject = $unit->subject;
        
        $topic->loadCount('questions');
        
        $questions = $topic->questions()
            ->with(['questionType', 'bloomsTaxonomy'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('question-bank.topics.show', compact('subject', 'unit', 'topic', 'questions'));
    }

    /**
     * Show the form for editing the specified topic.
     *
     * @param  \App\Models\Unit  $unit
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function edit(Unit $unit, Topic $topic)
    {
        // Ensure the topic belongs to the unit
        if ($topic->unit_id !== $unit->id) {
            abort(404);
        }
        
        $subject = $unit->subject;
        
        return view('question-bank.topics.edit', compact('subject', 'unit', 'topic'));
    }

    /**
     * Update the specified topic in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Unit $unit, Topic $topic)
    {
        // Ensure the topic belongs to the unit
        if ($topic->unit_id !== $unit->id) {
            abort(404);
        }
        
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('topics')->where(function ($query) use ($unit) {
                    return $query->where('unit_id', $unit->id);
                })->ignore($topic->id),
            ],
            'code' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('topics')->where(function ($query) use ($unit) {
                    return $query->where('unit_id', $unit->id);
                })->ignore($topic->id),
            ],
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('question-bank.units.topics.edit', [$unit, $topic])
                ->withErrors($validator)
                ->withInput();
        }

        $topic->update($validator->validated());
        
        $subject = $unit->subject;

        return redirect()->route('question-bank.units.topics.show', [$unit, $topic])
            ->with('success', 'Topic updated successfully.');
    }

    /**
     * Remove the specified topic from storage.
     *
     * @param  \App\Models\Unit  $unit
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function destroy(Unit $unit, Topic $topic)
    {
        // Ensure the topic belongs to the unit
        if ($topic->unit_id !== $unit->id) {
            abort(404);
        }
        
        // Check if topic has questions
        if ($topic->questions()->count() > 0) {
            return redirect()->route('question-bank.units.topics.show', [$unit, $topic])
                ->with('error', 'Cannot delete topic with associated questions. Please delete the questions first.');
        }

        $topic->delete();
        
        $subject = $unit->subject;

        return redirect()->route('question-bank.units.topics.index', $unit)
            ->with('success', 'Topic deleted successfully.');
    }

    /**
     * Toggle the active status of the specified topic.
     *
     * @param  \App\Models\Unit  $unit
     * @param  \App\Models\Topic  $topic
     * @return \Illuminate\Http\Response
     */
    public function toggleActive(Unit $unit, Topic $topic)
    {
        // Ensure the topic belongs to the unit
        if ($topic->unit_id !== $unit->id) {
            abort(404);
        }
        
        $topic->is_active = !$topic->is_active;
        $topic->save();

        $status = $topic->is_active ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Topic {$status} successfully.");
    }

    /**
     * Reorder topics.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Unit  $unit
     * @return \Illuminate\Http\Response
     */
    public function reorder(Request $request, Unit $unit)
    {
        $validator = Validator::make($request->all(), [
            'topics' => 'required|array',
            'topics.*' => 'required|integer|exists:topics,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $topicIds = $request->input('topics');
        
        // Update the order of each topic
        foreach ($topicIds as $index => $topicId) {
            $topic = Topic::find($topicId);
            
            // Ensure the topic belongs to the unit
            if ($topic && $topic->unit_id === $unit->id) {
                $topic->order = $index + 1;
                $topic->save();
            }
        }

        return response()->json(['success' => true]);
    }
}

