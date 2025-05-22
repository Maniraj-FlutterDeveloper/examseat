@extends('layouts.app')

@section('title', 'Student Details')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-graduate mr-2"></i> Student Details: {{ $student->name }}
                    </h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-12 text-right">
                            <a href="{{ route('students.edit', $student) }}" class="btn btn-primary">
                                <i class="fas fa-edit mr-1"></i> Edit Student
                            </a>
                            <a href="{{ route('students.print-card', $student) }}" class="btn btn-warning" target="_blank">
                                <i class="fas fa-print mr-1"></i> Print ID Card
                            </a>
                            <a href="{{ route('students.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-arrow-left mr-1"></i> Back to List
                            </a>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-info text-white">
                                    <h6 class="mb-0">Personal Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div class="avatar-circle">
                                            <span class="avatar-text">{{ strtoupper(substr($student->name, 0, 2)) }}</span>
                                        </div>
                                        <h4 class="mt-3">{{ $student->name }}</h4>
                                        <p class="text-muted">{{ $student->roll_number }}</p>
                                        <span class="badge badge-{{ $student->is_active ? 'success' : 'danger' }}">
                                            {{ $student->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>

                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%">Gender</th>
                                            <td>{{ ucfirst($student->gender ?? 'N/A') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Date of Birth</th>
                                            <td>{{ $student->date_of_birth ? $student->date_of_birth->format('M d, Y') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $student->email ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone</th>
                                            <td>{{ $student->phone ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Address</th>
                                            <td>{{ $student->address ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Special Needs</th>
                                            <td>
                                                @if($student->has_special_needs)
                                                    <span class="badge badge-warning">Yes</span>
                                                    <p class="mt-1 mb-0 small">{{ $student->special_needs_details }}</p>
                                                @else
                                                    <span class="badge badge-secondary">No</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">Academic Information</h6>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="width: 40%">Course</th>
                                            <td>
                                                @if($student->course)
                                                    <a href="{{ route('courses.show', $student->course) }}">
                                                        {{ $student->course->name }}
                                                    </a>
                                                @else
                                                    N/A
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Course Code</th>
                                            <td>{{ $student->course ? $student->course->code : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Department</th>
                                            <td>{{ $student->course ? ($student->course->department ?? 'N/A') : 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Year</th>
                                            <td>Year {{ $student->year }}</td>
                                        </tr>
                                        <tr>
                                            <th>Section</th>
                                            <td>{{ $student->section ?? 'N/A' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Joined On</th>
                                            <td>{{ $student->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    </table>

                                    <div class="mt-4">
                                        <h6>Student ID Card</h6>
                                        <div class="id-card-preview">
                                            <div class="id-card">
                                                <div class="id-card-header">
                                                    <h5 class="mb-0">STUDENT IDENTIFICATION CARD</h5>
                                                </div>
                                                <div class="id-card-body">
                                                    <div class="avatar-circle-sm">
                                                        <span class="avatar-text-sm">{{ strtoupper(substr($student->name, 0, 2)) }}</span>
                                                    </div>
                                                    <h6 class="mt-2 mb-0">{{ $student->name }}</h6>
                                                    <p class="text-muted small mb-2">{{ $student->roll_number }}</p>
                                                    <div class="id-card-details">
                                                        <p class="mb-1 small"><strong>Course:</strong> {{ $student->course ? $student->course->code : 'N/A' }}</p>
                                                        <p class="mb-1 small"><strong>Year:</strong> {{ $student->year }}</p>
                                                        <p class="mb-1 small"><strong>Section:</strong> {{ $student->section ?? 'N/A' }}</p>
                                                    </div>
                                                </div>
                                                <div class="id-card-footer">
                                                    <div class="barcode">
                                                        <i class="fas fa-barcode fa-2x"></i>
                                                    </div>
                                                    <p class="small mb-0">Valid until: {{ now()->addYears(1)->format('M Y') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center mt-3">
                                            <a href="{{ route('students.print-card', $student) }}" class="btn btn-sm btn-primary" target="_blank">
                                                <i class="fas fa-print mr-1"></i> Print ID Card
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card mb-4">
                                <div class="card-header bg-warning text-white">
                                    <h6 class="mb-0">Examination History</h6>
                                </div>
                                <div class="card-body">
                                    <div class="exam-stats mb-4">
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="card bg-light">
                                                    <div class="card-body text-center p-3">
                                                        <h3 class="mb-0">{{ $student->seatingAssignments->count() }}</h3>
                                                        <p class="small mb-0">Total Exams</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card bg-light">
                                                    <div class="card-body text-center p-3">
                                                        <h3 class="mb-0">{{ $student->seatingAssignments->where('is_present', true)->count() }}</h3>
                                                        <p class="small mb-0">Attended</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <h6>Recent Exams</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Exam</th>
                                                    <th>Room</th>
                                                    <th>Seat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($student->seatingAssignments->sortByDesc('seatingPlan.exam_date')->take(5) as $assignment)
                                                    <tr>
                                                        <td>{{ \Carbon\Carbon::parse($assignment->seatingPlan->exam_date)->format('M d') }}</td>
                                                        <td>
                                                            <a href="{{ route('seating-plans.show', $assignment->seatingPlan) }}">
                                                                {{ Str::limit($assignment->seatingPlan->title, 15) }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <a href="{{ route('rooms.show', $assignment->room) }}">
                                                                {{ $assignment->room->code }}
                                                            </a>
                                                        </td>
                                                        <td>{{ $assignment->seat_number }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No exam history found.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4">
                                        <h6>Upcoming Exams</h6>
                                        <div class="list-group">
                                            @php
                                                $upcomingExams = $student->seatingAssignments
                                                    ->filter(function($assignment) {
                                                        return $assignment->seatingPlan->exam_date >= date('Y-m-d');
                                                    })
                                                    ->sortBy('seatingPlan.exam_date')
                                                    ->take(3);
                                            @endphp

                                            @forelse($upcomingExams as $assignment)
                                                <div class="list-group-item list-group-item-action">
                                                    <div class="d-flex w-100 justify-content-between">
                                                        <h6 class="mb-1">{{ $assignment->seatingPlan->title }}</h6>
                                                        <small>{{ \Carbon\Carbon::parse($assignment->seatingPlan->exam_date)->format('M d') }}</small>
                                                    </div>
                                                    <p class="mb-1">
                                                        <small>
                                                            <i class="fas fa-clock mr-1"></i> {{ \Carbon\Carbon::parse($assignment->seatingPlan->start_time)->format('h:i A') }}
                                                            <i class="fas fa-door-open ml-2 mr-1"></i> {{ $assignment->room->name }} ({{ $assignment->room->code }})
                                                            <i class="fas fa-chair ml-2 mr-1"></i> Seat {{ $assignment->seat_number }}
                                                        </small>
                                                    </p>
                                                </div>
                                            @empty
                                                <div class="list-group-item text-center">
                                                    No upcoming exams found.
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .avatar-circle {
        width: 100px;
        height: 100px;
        background-color: #3e92cc;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }
    
    .avatar-text {
        color: white;
        font-size: 36px;
        font-weight: bold;
    }
    
    .avatar-circle-sm {
        width: 60px;
        height: 60px;
        background-color: #3e92cc;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 0 auto;
    }
    
    .avatar-text-sm {
        color: white;
        font-size: 24px;
        font-weight: bold;
    }
    
    .id-card-preview {
        display: flex;
        justify-content: center;
        padding: 10px;
    }
    
    .id-card {
        width: 240px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    .id-card-header {
        background-color: #0a2463;
        color: white;
        text-align: center;
        padding: 8px;
        font-size: 12px;
    }
    
    .id-card-body {
        padding: 15px;
        text-align: center;
        background-color: white;
    }
    
    .id-card-details {
        text-align: left;
        margin-top: 10px;
        border-top: 1px dashed #ddd;
        padding-top: 10px;
    }
    
    .id-card-footer {
        background-color: #f8f9fa;
        padding: 8px;
        text-align: center;
        border-top: 1px solid #ddd;
    }
    
    .barcode {
        margin-bottom: 5px;
    }
</style>
@endsection

