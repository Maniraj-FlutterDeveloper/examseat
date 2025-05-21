@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title">Bloom's Taxonomy Levels</h1>
        <a href="{{ route('admin.blooms-taxonomy.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Add New Level
        </a>
    </div>
    
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Level Name</th>
                            <th>Description</th>
                            <th>Order</th>
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
                                <td>{{ Str::limit($taxonomy->description, 50) }}</td>
                                <td>{{ $taxonomy->order }}</td>
                                <td>{{ $taxonomy->questions_count }}</td>
                                <td>{{ $taxonomy->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.blooms-taxonomy.show', $taxonomy->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blooms-taxonomy.edit', $taxonomy->id) }}" class="btn btn-sm btn-warning">
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
                                                            This level has {{ $taxonomy->questions_count }} questions associated with it. Deleting this level will remove the association from these questions.
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <form action="{{ route('admin.blooms-taxonomy.destroy', $taxonomy->id) }}" method="POST" class="d-inline">
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
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-brain fa-3x text-muted mb-3"></i>
                                    <p>No Bloom's Taxonomy levels found.</p>
                                    <a href="{{ route('admin.blooms-taxonomy.create') }}" class="btn btn-primary">Add Level</a>
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
    
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">About Bloom's Taxonomy</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>What is Bloom's Taxonomy?</h5>
                    <p>Bloom's Taxonomy is a hierarchical ordering of cognitive skills that can help teachers teach and students learn. It categorizes learning objectives into six levels of complexity and specificity.</p>
                    
                    <h5 class="mt-4">The Six Levels (Revised Version):</h5>
                    <ol>
                        <li><strong>Remember:</strong> Recall facts and basic concepts</li>
                        <li><strong>Understand:</strong> Explain ideas or concepts</li>
                        <li><strong>Apply:</strong> Use information in new situations</li>
                        <li><strong>Analyze:</strong> Draw connections among ideas</li>
                        <li><strong>Evaluate:</strong> Justify a stand or decision</li>
                        <li><strong>Create:</strong> Produce new or original work</li>
                    </ol>
                </div>
                <div class="col-md-6">
                    <h5>How to Use Bloom's Taxonomy in Question Creation:</h5>
                    <p>When creating questions, assign them to the appropriate cognitive level to ensure a balanced assessment that tests various thinking skills.</p>
                    
                    <h5 class="mt-4">Example Verbs for Each Level:</h5>
                    <ul>
                        <li><strong>Remember:</strong> Define, List, Recall, Name, Identify</li>
                        <li><strong>Understand:</strong> Explain, Describe, Discuss, Interpret</li>
                        <li><strong>Apply:</strong> Solve, Implement, Use, Demonstrate</li>
                        <li><strong>Analyze:</strong> Compare, Contrast, Examine, Categorize</li>
                        <li><strong>Evaluate:</strong> Judge, Critique, Justify, Defend</li>
                        <li><strong>Create:</strong> Design, Develop, Formulate, Compose</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

