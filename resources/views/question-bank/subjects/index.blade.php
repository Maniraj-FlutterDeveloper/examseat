@extends('layouts.question-bank')

@section('title', 'Subjects')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Subjects</h1>
    <a href="{{ route('question-bank.subjects.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Subject
    </a>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold">All Subjects</h6>
        <form action="{{ route('question-bank.subjects.search') }}" method="GET" class="d-flex">
            <input type="text" name="query" class="form-control form-control-sm me-2" placeholder="Search subjects..." value="{{ $query ?? '' }}">
            <button type="submit" class="btn btn-sm btn-primary">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <div class="card-body">
        @if($subjects->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Units</th>
                            <th>Topics</th>
                            <th>Questions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subjects as $subject)
                            <tr>
                                <td>
                                    <a href="{{ route('question-bank.subjects.show', $subject) }}" class="fw-bold text-decoration-none">
                                        {{ $subject->name }}
                                    </a>
                                </td>
                                <td>{{ $subject->code }}</td>
                                <td>{{ $subject->units_count }}</td>
                                <td>{{ $subject->topics_count }}</td>
                                <td>{{ $subject->questions_count }}</td>
                                <td>
                                    @if($subject->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('question-bank.subjects.show', $subject) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('question-bank.subjects.edit', $subject) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('question-bank.subjects.toggle-active', $subject) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $subject->is_active ? 'btn-secondary' : 'btn-success' }}" data-bs-toggle="tooltip" title="{{ $subject->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas {{ $subject->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('question-bank.subjects.destroy', $subject) }}" method="POST" class="d-inline delete-form">
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
            <div class="d-flex justify-content-center mt-4">
                {{ $subjects->links() }}
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i> No subjects found. 
                <a href="{{ route('question-bank.subjects.create') }}" class="alert-link">Create your first subject</a>.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Confirm delete
    $('.delete-form').on('submit', function(e) {
        e.preventDefault();
        if (confirm('Are you sure you want to delete this subject? This action cannot be undone.')) {
            this.submit();
        }
    });
</script>
@endpush

