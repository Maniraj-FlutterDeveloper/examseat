<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Room;
use App\Models\SeatingPlan;
use App\Models\QuestionPaper;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Get counts for dashboard stats
        $totalStudents = Student::count();
        $totalRooms = Room::count();
        $totalSeatingPlans = SeatingPlan::count();
        $totalQuestionPapers = QuestionPaper::count();
        
        // Get recent seating plans
        $recentSeatingPlans = SeatingPlan::orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        // Get recent question papers
        $recentQuestionPapers = QuestionPaper::with('subject')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        return view('dashboard', compact(
            'totalStudents',
            'totalRooms',
            'totalSeatingPlans',
            'totalQuestionPapers',
            'recentSeatingPlans',
            'recentQuestionPapers'
        ));
    }
}

