<?php

namespace App\Http\Controllers\Mobile;

use App\Http\Controllers\Controller;
use App\Models\QuestionPaper;
use App\Models\SeatingPlan;
use App\Models\Student;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StudentPortalController extends Controller
{
    /**
     * Display the student dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $upcomingExams = $this->getUpcomingExams($student);
        $recentNotifications = $this->getRecentNotifications($student);
        
        return view('mobile.dashboard', [
            'student' => $student,
            'upcomingExams' => $upcomingExams,
            'recentNotifications' => $recentNotifications,
        ]);
    }
    
    /**
     * Display the student profile.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        return view('mobile.profile', [
            'student' => $student,
        ]);
    }
    
    /**
     * Update the student profile.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_picture' => 'nullable|image|max:2048',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $student->name = $request->name;
        $student->email = $request->email;
        $student->phone = $request->phone;
        
        if ($request->filled('password')) {
            $student->password = Hash::make($request->password);
        }
        
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($student->profile_picture) {
                \Storage::disk('public')->delete($student->profile_picture);
            }
            
            // Store new profile picture
            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $student->profile_picture = $path;
        }
        
        $student->save();
        
        return redirect()->route('mobile.profile')
            ->with('success', 'Profile updated successfully.');
    }
    
    /**
     * Display the student's seating plans.
     *
     * @return \Illuminate\Http\Response
     */
    public function seatingPlans()
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $seatingPlans = SeatingPlan::whereHas('students', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })->orderBy('exam_date', 'desc')->get();
        
        return view('mobile.seating_plans', [
            'student' => $student,
            'seatingPlans' => $seatingPlans,
        ]);
    }
    
    /**
     * Display a specific seating plan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewSeatingPlan($id)
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $seatingPlan = SeatingPlan::findOrFail($id);
        
        // Check if the student is assigned to this seating plan
        $isAssigned = $seatingPlan->students()->where('student_id', $student->id)->exists();
        
        if (!$isAssigned) {
            return redirect()->route('mobile.seating_plans')
                ->with('error', 'You are not assigned to this seating plan.');
        }
        
        // Get the student's seat details
        $seatDetails = $seatingPlan->students()
            ->where('student_id', $student->id)
            ->first()
            ->pivot;
        
        // Get the room details
        $room = $seatDetails->room;
        
        return view('mobile.view_seating_plan', [
            'student' => $student,
            'seatingPlan' => $seatingPlan,
            'seatDetails' => $seatDetails,
            'room' => $room,
        ]);
    }
    
    /**
     * Display the student's exam schedule.
     *
     * @return \Illuminate\Http\Response
     */
    public function examSchedule()
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $examSchedule = $this->getExamSchedule($student);
        
        return view('mobile.exam_schedule', [
            'student' => $student,
            'examSchedule' => $examSchedule,
        ]);
    }
    
    /**
     * Display the student's notifications.
     *
     * @return \Illuminate\Http\Response
     */
    public function notifications()
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $notifications = $this->getAllNotifications($student);
        
        return view('mobile.notifications', [
            'student' => $student,
            'notifications' => $notifications,
        ]);
    }
    
    /**
     * Display a specific notification.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewNotification($id)
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $notification = Notification::findOrFail($id);
        
        // Check if the notification is for this student
        if ($notification->recipient_id != $student->id || $notification->recipient_type != 'student') {
            return redirect()->route('mobile.notifications')
                ->with('error', 'You do not have access to this notification.');
        }
        
        // Mark the notification as read
        if (!$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }
        
        return view('mobile.view_notification', [
            'student' => $student,
            'notification' => $notification,
        ]);
    }
    
    /**
     * Display the student's question papers.
     *
     * @return \Illuminate\Http\Response
     */
    public function questionPapers()
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $questionPapers = $this->getQuestionPapers($student);
        
        return view('mobile.question_papers', [
            'student' => $student,
            'questionPapers' => $questionPapers,
        ]);
    }
    
    /**
     * Display a specific question paper.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function viewQuestionPaper($id)
    {
        $student = $this->getAuthenticatedStudent();
        
        if (!$student) {
            return redirect()->route('mobile.login');
        }
        
        $questionPaper = QuestionPaper::findOrFail($id);
        
        // Check if the question paper is available for this student
        if (!$this->isQuestionPaperAvailable($questionPaper, $student)) {
            return redirect()->route('mobile.question_papers')
                ->with('error', 'This question paper is not available for you.');
        }
        
        return view('mobile.view_question_paper', [
            'student' => $student,
            'questionPaper' => $questionPaper,
        ]);
    }
    
    /**
     * Display the login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {
        if (Auth::guard('student')->check()) {
            return redirect()->route('mobile.dashboard');
        }
        
        return view('mobile.login');
    }
    
    /**
     * Handle a login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roll_number' => 'required|string',
            'password' => 'required|string',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $credentials = [
            'roll_number' => $request->roll_number,
            'password' => $request->password,
        ];
        
        if (Auth::guard('student')->attempt($credentials, $request->filled('remember'))) {
            return redirect()->intended(route('mobile.dashboard'));
        }
        
        return redirect()->back()
            ->withInput($request->only('roll_number', 'remember'))
            ->withErrors(['roll_number' => 'These credentials do not match our records.']);
    }
    
    /**
     * Log the user out.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Auth::guard('student')->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('mobile.login');
    }
    
    /**
     * Get the authenticated student.
     *
     * @return \App\Models\Student|null
     */
    protected function getAuthenticatedStudent()
    {
        return Auth::guard('student')->user();
    }
    
    /**
     * Get upcoming exams for the student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getUpcomingExams($student)
    {
        return SeatingPlan::whereHas('students', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
        ->where('exam_date', '>=', now())
        ->orderBy('exam_date', 'asc')
        ->take(5)
        ->get();
    }
    
    /**
     * Get recent notifications for the student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentNotifications($student)
    {
        return Notification::where('recipient_id', $student->id)
            ->where('recipient_type', 'student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }
    
    /**
     * Get all notifications for the student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAllNotifications($student)
    {
        return Notification::where('recipient_id', $student->id)
            ->where('recipient_type', 'student')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }
    
    /**
     * Get the exam schedule for the student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getExamSchedule($student)
    {
        return SeatingPlan::whereHas('students', function ($query) use ($student) {
            $query->where('student_id', $student->id);
        })
        ->orderBy('exam_date', 'asc')
        ->get();
    }
    
    /**
     * Get question papers for the student.
     *
     * @param  \App\Models\Student  $student
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getQuestionPapers($student)
    {
        // Get the student's course
        $course = $student->course;
        
        // Get question papers for the student's course
        return QuestionPaper::whereHas('subject', function ($query) use ($course) {
            $query->whereHas('courses', function ($query) use ($course) {
                $query->where('course_id', $course->id);
            });
        })
        ->where('is_published', true)
        ->orderBy('created_at', 'desc')
        ->get();
    }
    
    /**
     * Check if a question paper is available for the student.
     *
     * @param  \App\Models\QuestionPaper  $questionPaper
     * @param  \App\Models\Student  $student
     * @return bool
     */
    protected function isQuestionPaperAvailable($questionPaper, $student)
    {
        // Get the student's course
        $course = $student->course;
        
        // Check if the question paper's subject is for the student's course
        return $questionPaper->subject->courses()->where('course_id', $course->id)->exists();
    }
}

