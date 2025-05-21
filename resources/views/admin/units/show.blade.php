@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Unit Details</h1>
        <div>
            <a href="{{ route('admin.units.edit', $unit->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit Unit
            </a>
            <a href="{{ route('admin.units.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Units
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Unit Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width: 30%">ID</th>
                            <td>{{ $unit->id }}</td>
                        </tr>
                        <tr>
                            <th>Unit Name</th>
                            <td>{{ $unit->unit_name }}</td>
                        </tr>
                        <tr>
                            <th>Unit Code</th>
                            <td>{{ $unit->unit_code ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Subject</th>
                            <td>
                                <a href="{{ route('admin.subjects.show', $unit->subject_id) }}">
                                    {{ $unit->subject->subject_name }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td>{{ $unit->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Order</th>
                            <td>{{ $unit->order }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($unit->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ $unit->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <td>{{ $unit->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Topics</h5>
                    <a href="{{ route('admin.topics.create', ['unit_id' => $unit->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Topic
                    </a>
                </div>
                <div class="card-body">
                    @if($unit->topics->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Topic Name</th>
                                        <th>Topic Code</th>
                                        <th>Questions</th>
                                        <th>Order</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->topics as $topic)
                                        <tr>
                                            <td>{{ $topic->id }}</td>
                                            <td>{{ $topic->topic_name }}</td>
                                            <td>{{ $topic->topic_code ?: 'N/A' }}</td>
                                            <td>{{ $topic->questions->count() }}</td>
                                            <td>{{ $topic->order }}</td>
                                            <td>
                                                @if($topic->is_active)
                                                    <span class="badge bg-success">Active</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.topics.show', $topic->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('admin.topics.edit', $topic->id) }}" class="btn btn-sm btn-warning">
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
                            <p>No topics found for this unit.</p>
                            <a href="{{ route('admin.topics.create', ['unit_id' => $unit->id]) }}" class="btn btn-primary">Add Topic</a>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Unit Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Topics</h5>
                                    <h2 class="mb-0">{{ $unit->topics->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Questions</h5>
                                    <h2 class="mb-0">{{ $unit->topics->flatMap->questions->count() }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h5 class="card-title">Active Topics</h5>
                                    <h2 class="mb-0">{{ $unit->topics->where('is_active', true)->count() }}</h2>
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

