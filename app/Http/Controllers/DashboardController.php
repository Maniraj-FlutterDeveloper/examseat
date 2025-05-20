<?php

namespace App\Http\Controllers;

use App\Models\Block;
use App\Models\Room;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Question;
use App\Models\QuestionPaper;
use App\Models\SeatingPlan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stats = [
            'blocks' => Block::count(),
            'rooms' => Room::count(),
            'students' => Student::count(),
            'subjects' => Subject::count(),
            'questions' => Question::count(),
            'questionPapers' => QuestionPaper::count(),
            'seatingPlans' => SeatingPlan::count(),
        ];

        $recentSeatingPlans = SeatingPlan::with(['room', 'student'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $recentQuestionPapers = QuestionPaper::with('subject')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'recentSeatingPlans', 'recentQuestionPapers'));
    }
}
