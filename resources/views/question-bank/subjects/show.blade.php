@extends('layouts.question-bank')

@section('title', $subject->name)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('question-bank.subjects.index') }}">Subjects</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $subject->name }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ $subject->name }}</h1>
    <div>
        <a href="{{ route('question-bank.subjects.edit', $subject) }}" class="btn btn-warning">
            <i class="fas fa-edit"></i> Edit Subject
        </a>
        <a href="{{ route('question-bank.subjects.units.create', $subject) }}" class="btn btn-primary ms-2">
            <i class="fas fa-plus"></i> Add Unit
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Subject Details</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="fw-bold">Code:</label>
                    <p>{{ $subject->code ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Description:</label>
                    <p>{{ $subject->description ?? 'No description available.' }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Status:</label>
                    @if($subject->is_active)
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-danger">Inactive</span>
                    @endif
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Created:</label>
                    <p>{{ $subject->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div class="mb-3">
                    <label class="fw-bold">Last Updated:</label>
                    <p>{{ $subject->updated_at->format('M d, Y h:i A') }}</p>
                </div>
            </div>
        </div>
        
        <div class="card shadow mb-4 fade-in">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h5 class="fw-bold text-primary">{{ $subject->units_count }}</h5>
                            <small class="text-muted">Units</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h5 class="fw-bold text-success">{{ $subject->topics_count }}</h5>
                            <small class="text-muted">Topics</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <h5 class="fw-bold text-info">{{ $subject->questions_count }}</h5>
                            <small class="text-muted">Questions</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card shadow fade-in">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold">Units</h6>
                <a href="{{ route('question-bank.subjects.units.create', $subject) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> Add Unit
                </a>
            </div>
            <div class="card-body">
                @if($subject->units->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Name</th>
                                    <th>Code</th>
                                    <th>Topics</th>
                                    <th>Questions</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="units-table-body">
                                @foreach($subject->units as $unit)
                                    <tr data-id="{{ $unit->id }}">
                                        <td>{{ $unit->order }}</td>
                                        <td>
                                            <a href="{{ route('question-bank.subjects.units.show', [$subject, $unit]) }}" class="fw-bold text-decoration-none">
                                                {{ $unit->name }}
                                            </a>
                                        </td>
                                        <td>{{ $unit->code }}</td>
                                        <td>{{ $unit->topics_count }}</td>
                                        <td>{{ $unit->questions_count }}</td>
                                        <td>
                                            @if($unit->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('question-bank.subjects.units.show', [$subject, $unit]) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('question-bank.subjects.units.edit', [$subject, $unit]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('question-bank.subjects.units.toggle-active', [$subject, $unit]) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-sm {{ $unit->is_active ? 'btn-secondary' : 'btn-success' }}" data-bs-toggle="tooltip" title="{{ $unit->is_active ? 'Deactivate' : 'Activate' }}">
                                                        <i class="fas {{ $unit->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                    </button>
                                                </form>
                                                <form action="{{ route('question-bank.subjects.units.destroy', [$subject, $unit]) }}" method="POST" class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i> No units found for this subject. 
                        <a href="{{ route('question-bank.subjects.units.create', $subject) }}" class="alert-link">Create your first unit</a>.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirm delete
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this unit? This action cannot be undone.')) {
            this.submit();
        }
    });
</script>
@endpush

