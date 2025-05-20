@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Bloom's Taxonomy Levels</h1>
        <a href="{{ route('blooms-taxonomy.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Add New Level
        </a>
    </div>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">About Bloom's Taxonomy</h5>
                </div>
                <div class="card-body">
                    <p>Bloom's Taxonomy is a hierarchical ordering of cognitive skills that can help teachers teach and students learn. The taxonomy was first presented in 1956 and was later revised in 2001.</p>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="fw-bold">Original Taxonomy (1956)</h6>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Knowledge</li>
                                <li class="list-group-item">Comprehension</li>
                                <li class="list-group-item">Application</li>
                                <li class="list-group-item">Analysis</li>
                                <li class="list-group-item">Synthesis</li>
                                <li class="list-group-item">Evaluation</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold">Revised Taxonomy (2001)</h6>
                            <ol class="list-group list-group-numbered">
                                <li class="list-group-item">Remember</li>
                                <li class="list-group-item">Understand</li>
                                <li class="list-group-item">Apply</li>
                                <li class="list-group-item">Analyze</li>
                                <li class="list-group-item">Evaluate</li>
                                <li class="list-group-item">Create</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Level Name</th>
                            <th>Description</th>
                            <th>Questions</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bloomsTaxonomies as $taxonomy)
                            <tr>
                                <td>{{ $taxonomy->id }}</td>
                                <td>{{ $taxonomy->level_name }}</td>
                                <td>{{ Str::limit($taxonomy->description, 100) }}</td>
                                <td>{{ $taxonomy->questions_count }}</td>
                                <td>{{ $taxonomy->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('blooms-taxonomy.show', $taxonomy->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('blooms-taxonomy.edit', $taxonomy->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $taxonomy->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Delete Modal -->
                                    <div class="modal fade" id="deleteModal{{ $taxonomy->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $taxonomy->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="deleteModalLabel{{ $taxonomy->id }}">Confirm Delete</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Are you sure you want to delete the Bloom's Taxonomy level <strong>{{ $taxonomy->level_name }}</strong>?
                                                    @if($taxonomy->questions_count > 0)
                                                        <div class="alert alert-warning mt-3">
                                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                                            This level has {{ $taxonomy->questions_count }} questions associated with it. You cannot delete a level that has questions associated with it.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('blooms-taxonomy.destroy', $taxonomy->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger" {{ $taxonomy->questions_count > 0 ? 'disabled' : '' }}>Delete</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-brain fa-3x text-muted mb-3"></i>
                                    <p>No Bloom's Taxonomy levels found.</p>
                                    <a href="{{ route('blooms-taxonomy.create') }}" class="btn btn-primary">Add Level</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-center mt-4">
                {{ $bloomsTaxonomies->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

