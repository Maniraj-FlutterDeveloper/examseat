@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Unit Details</h1>
        <div>
            <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('units.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Units
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Unit Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $unit->id }}</td>
                        </tr>
                        <tr>
                            <th>Unit Name:</th>
                            <td>{{ $unit->unit_name }}</td>
                        </tr>
                        <tr>
                            <th>Unit Number:</th>
                            <td>{{ $unit->unit_number }}</td>
                        </tr>
                        <tr>
                            <th>Subject:</th>
                            <td>
                                <a href="{{ route('subjects.show', $unit->subject_id) }}">
                                    {{ $unit->subject->subject_name }} ({{ $unit->subject->subject_code }})
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $unit->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Topics:</th>
                            <td>{{ $unit->topics->count() }}</td>
                        </tr>
                        <tr>
                            <th>Questions:</th>
                            <td>{{ $unit->questions_count }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $unit->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $unit->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Topics in this Unit</h5>
                    <a href="{{ route('topics.create', ['unit_id' => $unit->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Topic
                    </a>
                </div>
                <div class="card-body">
                    @if($unit->topics->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Topic Name</th>
                                        <th>Topic Number</th>
                                        <th>Questions</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unit->topics as $topic)
                                        <tr>
                                            <td>{{ $topic->topic_name }}</td>
                                            <td>{{ $topic->topic_number }}</td>
                                            <td>{{ $topic->questions->count() }}</td>
                                            <td>{{ $topic->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('topics.show', $topic->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('topics.edit', $topic->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteTopicModal{{ $topic->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Topic Modal -->
                                                <div class="modal fade" id="deleteTopicModal{{ $topic->id }}" tabindex="-1" aria-labelledby="deleteTopicModalLabel{{ $topic->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteTopicModalLabel{{ $topic->id }}">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete the topic <strong>{{ $topic->topic_name }}</strong>?
                                                                @if($topic->questions->count() > 0)
                                                                    <div class="alert alert-warning mt-3">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                        This topic has {{ $topic->questions->count() }} questions associated with it. Deleting this topic will also delete all associated questions.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('topics.destroy', $topic->id) }}" method="POST" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-book-reader fa-3x text-muted mb-3"></i>
                            <p>No topics have been added to this unit yet.</p>
                            <a href="{{ route('topics.create', ['unit_id' => $unit->id]) }}" class="btn btn-primary">Add Topic</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

