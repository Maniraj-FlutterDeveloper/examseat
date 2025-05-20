@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h1 class="page-title">Dashboard</h1>
    
    <div class="row">
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $stats['blocks'] }}</div>
                            <div class="stats-text">Blocks</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-building"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $stats['rooms'] }}</div>
                            <div class="stats-text">Rooms</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-door-open"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $stats['students'] }}</div>
                            <div class="stats-text">Students</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="stats-number">{{ $stats['subjects'] }}</div>
                            <div class="stats-text">Subjects</div>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Seating Plans</h5>
                    <a href="{{ route('seating-plans.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentSeatingPlans->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Exam</th>
                                        <th>Room</th>
                                        <th>Student</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentSeatingPlans as $plan)
                                        <tr>
                                            <td>{{ $plan->exam_name }}</td>
                                            <td>{{ $plan->room->room_number }}</td>
                                            <td>{{ $plan->student->name }}</td>
                                            <td>{{ $plan->exam_date->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-chair fa-3x text-muted mb-3"></i>
                            <p>No seating plans created yet.</p>
                            <a href="{{ route('seating-plans.create') }}" class="btn btn-primary">Create Seating Plan</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Question Papers</h5>
                    <a href="{{ route('question-papers.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentQuestionPapers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Subject</th>
                                        <th>Marks</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentQuestionPapers as $paper)
                                        <tr>
                                            <td>{{ $paper->title }}</td>
                                            <td>{{ $paper->subject->subject_name }}</td>
                                            <td>{{ $paper->total_marks }}</td>
                                            <td>{{ $paper->created_at->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
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
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('seating-plans.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-chair fa-2x mb-2"></i><br>
                                Create Seating Plan
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('question-papers.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-file-alt fa-2x mb-2"></i><br>
                                Generate Question Paper
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
                            <a href="{{ route('students.create') }}" class="btn btn-outline-primary w-100 py-3">
                                <i class="fas fa-user-graduate fa-2x mb-2"></i><br>
                                Add Student
                            </a>
                        </div>
                        <div class="col-md-3 col-sm-6 mb-3">
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
@endsection

@push('scripts')
<script>
    // Add any dashboard-specific JavaScript here
</script>
@endpush

