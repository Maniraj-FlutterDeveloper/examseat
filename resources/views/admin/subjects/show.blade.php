@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Subject Details</h1>
        <div>
            <a href="{{ route('admin.subjects.edit', $subject->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Subject
            </a>
            <a href="{{ route('admin.subjects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Subjects
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subject Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $subject->id }}</td>
                        </tr>
                        <tr>
                            <th>Subject Name</th>
                            <td>{{ $subject->subject_name }}</td>
                        </tr>
                        <tr>
                            <th>Subject Code</th>
                            <td>{{ $subject->subject_code }}</td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $subject->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($subject->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $subject->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $subject->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Units</h5>
                    <a href="{{ route('admin.units.create', ['subject_id' => $subject->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Unit
                    </a>
                </div>
                <div class="card-body">
                    @if($units->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Unit Name</th>
                                        <th>Unit Code</th>
                                        <th>Topics</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($units as $unit)
                                        <tr>
                                            <td>{{ $unit->id }}</td>
                                            <td>{{ $unit->unit_name }}</td>
                                            <td>{{ $unit->unit_code }}</td>
                                            <td>{{ $unit->topics->count() }}</td>
                                            <td>
                                                @if($unit->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.units.show', $unit->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p>No units found for this subject.</p>
                            <a href="{{ route('admin.units.create', ['subject_id' => $subject->id]) }}" class="btn btn-primary">Add Unit</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Subject Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Units</h5>
                                    <h2 class="mb-0">{{ $units->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Topics</h5>
                                    <h2 class="mb-0">{{ $units->flatMap->topics->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Questions</h5>
                                    <h2 class="mb-0">{{ $units->flatMap->topics->flatMap->questions->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Blueprints</h5>
                                    <h2 class="mb-0">{{ $subject->blueprints()->count() }}</h2>
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

