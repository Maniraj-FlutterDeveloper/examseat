@extends('layouts.app')

@section('title', 'Dashboard - Exam Seat Management System')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Dashboard</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-4">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Total Students</h6>
                                        <h3 class="mb-0">{{ $totalStudents ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between bg-primary border-top-0">
                                <a href="{{ route('students.index') }}" class="text-white-50 text-decoration-none">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Total Rooms</h6>
                                        <h3 class="mb-0">{{ $totalRooms ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-door-open fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between bg-success border-top-0">
                                <a href="{{ route('rooms.index') }}" class="text-white-50 text-decoration-none">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-warning text-white h-100">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Seating Plans</h6>
                                        <h3 class="mb-0">{{ $totalSeatingPlans ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-chair fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between bg-warning border-top-0">
                                <a href="{{ route('seating-plans.index') }}" class="text-white-50 text-decoration-none">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-3 mb-4">
                        <div class="card bg-danger text-white h-100">
                            <div class="card-body py-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-white-50">Question Papers</h6>
                                        <h3 class="mb-0">{{ $totalQuestionPapers ?? 0 }}</h3>
                                    </div>
                                    <i class="fas fa-file-alt fa-3x opacity-50"></i>
                                </div>
                            </div>
                            <div class="card-footer d-flex align-items-center justify-content-between bg-danger border-top-0">
                                <a href="{{ route('question-papers.index') }}" class="text-white-50 text-decoration-none">View Details</a>
                                <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Seating Plans</h5>
                                <a href="{{ route('seating-plans.index') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @if(isset($recentSeatingPlans) && count($recentSeatingPlans) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Name</th>
                                                    <th>Date</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentSeatingPlans as $plan)
                                                <tr>
                                                    <td>{{ $plan->name }}</td>
                                                    <td>{{ $plan->exam_date }}</td>
                                                    <td>
                                                        @if($plan->status == 'active')
                                                            <span class="badge bg-success">Active</span>
                                                        @elseif($plan->status == 'draft')
                                                            <span class="badge bg-warning">Draft</span>
                                                        @else
                                                            <span class="badge bg-secondary">Archived</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                                        <p>No seating plans created yet.</p>
                                        <a href="{{ route('seating-plans.create') }}" class="btn btn-primary">Create Seating Plan</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Recent Question Papers</h5>
                                <a href="{{ route('question-papers.index') }}" class="btn btn-sm btn-primary">View All</a>
                            </div>
                            <div class="card-body">
                                @if(isset($recentQuestionPapers) && count($recentQuestionPapers) > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Title</th>
                                                    <th>Subject</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($recentQuestionPapers as $paper)
                                                <tr>
                                                    <td>{{ $paper->title }}</td>
                                                    <td>{{ $paper->subject->name }}</td>
                                                    <td>
                                                        @if($paper->status == 'published')
                                                            <span class="badge bg-success">Published</span>
                                                        @elseif($paper->status == 'draft')
                                                            <span class="badge bg-warning">Draft</span>
                                                        @else
                                                            <span class="badge bg-secondary">Archived</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <p>No question papers created yet.</p>
                                        <a href="{{ route('question-papers.create') }}" class="btn btn-primary">Create Question Paper</a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('seating-plans.create') }}" class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-chair fa-2x mb-2"></i><br>
                                            Create Seating Plan
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('question-papers.create') }}" class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                                            Generate Question Paper
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('students.create') }}" class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                                            Add Student
                                        </a>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ route('rooms.create') }}" class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-door-open fa-2x mb-2"></i><br>
                                            Add Room
                                        </a>
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

