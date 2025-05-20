@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Subject Details</h1>
        <div>
            <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('subjects.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Subjects
            </a>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Subject Information</h5>
                </div>
                <div class="card-body">
                    <table class="table">
                        <tr>
                            <th style="width: 30%">ID:</th>
                            <td>{{ $subject->id }}</td>
                        </tr>
                        <tr>
                            <th>Subject Name:</th>
                            <td>{{ $subject->subject_name }}</td>
                        </tr>
                        <tr>
                            <th>Subject Code:</th>
                            <td>{{ $subject->subject_code }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $subject->description ?: 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Units:</th>
                            <td>{{ $subject->units->count() }}</td>
                        </tr>
                        <tr>
                            <th>Topics:</th>
                            <td>{{ $subject->topics_count }}</td>
                        </tr>
                        <tr>
                            <th>Questions:</th>
                            <td>{{ $subject->questions_count }}</td>
                        </tr>
                        <tr>
                            <th>Created At:</th>
                            <td>{{ $subject->created_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Updated At:</th>
                            <td>{{ $subject->updated_at->format('M d, Y H:i:s') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Units in this Subject</h5>
                    <a href="{{ route('units.create', ['subject_id' => $subject->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-plus-circle me-1"></i>Add Unit
                    </a>
                </div>
                <div class="card-body">
                    @if($subject->units->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Unit Name</th>
                                        <th>Unit Number</th>
                                        <th>Topics</th>
                                        <th>Questions</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subject->units as $unit)
                                        <tr>
                                            <td>{{ $unit->unit_name }}</td>
                                            <td>{{ $unit->unit_number }}</td>
                                            <td>{{ $unit->topics->count() }}</td>
                                            <td>{{ $unit->questions_count }}</td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('units.show', $unit->id) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('units.edit', $unit->id) }}" class="btn btn-sm btn-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUnitModal{{ $unit->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                
                                                <!-- Delete Unit Modal -->
                                                <div class="modal fade" id="deleteUnitModal{{ $unit->id }}" tabindex="-1" aria-labelledby="deleteUnitModalLabel{{ $unit->id }}" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="deleteUnitModalLabel{{ $unit->id }}">Confirm Delete</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                Are you sure you want to delete the unit <strong>{{ $unit->unit_name }}</strong>?
                                                                @if($unit->topics->count() > 0)
                                                                    <div class="alert alert-warning mt-3">
                                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                                        This unit has {{ $unit->topics->count() }} topics and {{ $unit->questions_count }} questions associated with it. Deleting this unit will also delete all associated topics and questions.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                <form action="{{ route('units.destroy', $unit->id) }}" method="POST" class="d-inline">
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
                            <i class="fas fa-book-open fa-3x text-muted mb-3"></i>
                            <p>No units have been added to this subject yet.</p>
                            <a href="{{ route('units.create', ['subject_id' => $subject->id]) }}" class="btn btn-primary">Add Unit</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

