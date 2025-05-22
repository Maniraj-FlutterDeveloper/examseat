@extends('layouts.question-bank')

@section('title', 'Bloom\'s Taxonomy')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bloom's Taxonomy</h1>
    <a href="{{ route('question-bank.blooms-taxonomy.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Level
    </a>
</div>

<div class="card shadow fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">Cognitive Levels</h6>
    </div>
    <div class="card-body">
        @if($bloomsLevels->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Level</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Questions</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bloomsLevels as $level)
                            <tr>
                                <td>{{ $level->level }}</td>
                                <td>
                                    <a href="{{ route('question-bank.blooms-taxonomy.show', $level) }}" class="fw-bold text-decoration-none">
                                        {{ $level->name }}
                                    </a>
                                </td>
                                <td>{{ Str::limit($level->description, 50) }}</td>
                                <td>{{ $level->questions_count }}</td>
                                <td>
                                    @if($level->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('question-bank.blooms-taxonomy.show', $level) }}" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('question-bank.blooms-taxonomy.edit', $level) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('question-bank.blooms-taxonomy.toggle-active', $level) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm {{ $level->is_active ? 'btn-secondary' : 'btn-success' }}" data-bs-toggle="tooltip" title="{{ $level->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas {{ $level->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('question-bank.blooms-taxonomy.destroy', $level) }}" method="POST" class="d-inline delete-form">
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
                <i class="fas fa-info-circle me-2"></i> No Bloom's Taxonomy levels found. 
                <a href="{{ route('question-bank.blooms-taxonomy.create') }}" class="alert-link">Create your first level</a>.
            </div>
        @endif
    </div>
</div>

<div class="card shadow mt-4 fade-in">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold">About Bloom's Taxonomy</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p>Bloom's Taxonomy is a hierarchical ordering of cognitive skills that can help teachers teach and students learn. The taxonomy was first presented in 1956 and was later revised in 2001.</p>
                
                <p>The cognitive domain involves knowledge and the development of intellectual skills. This includes the recall or recognition of specific facts, procedural patterns, and concepts that serve in the development of intellectual abilities and skills.</p>
                
                <p>The six major categories of cognitive processes, from the simplest to the most complex, are:</p>
                
                <ol>
                    <li><strong>Remember:</strong> Retrieving relevant knowledge from long-term memory.</li>
                    <li><strong>Understand:</strong> Determining the meaning of instructional messages.</li>
                    <li><strong>Apply:</strong> Carrying out or using a procedure in a given situation.</li>
                    <li><strong>Analyze:</strong> Breaking material into its constituent parts and detecting how the parts relate to one another and to an overall structure or purpose.</li>
                    <li><strong>Evaluate:</strong> Making judgments based on criteria and standards.</li>
                    <li><strong>Create:</strong> Putting elements together to form a novel, coherent whole or make an original product.</li>
                </ol>
            </div>
            <div class="col-md-6">
                <div class="card bg-light">
                    <div class="card-body">
                        <h5 class="card-title">Example Verbs for Each Level</h5>
                        <ul>
                            <li><strong>Remember:</strong> define, duplicate, list, memorize, recall, repeat, reproduce, state</li>
                            <li><strong>Understand:</strong> classify, describe, discuss, explain, identify, locate, recognize, report, select, translate, paraphrase</li>
                            <li><strong>Apply:</strong> choose, demonstrate, dramatize, employ, illustrate, interpret, operate, schedule, sketch, solve, use, write</li>
                            <li><strong>Analyze:</strong> appraise, compare, contrast, criticize, differentiate, discriminate, distinguish, examine, experiment, question, test</li>
                            <li><strong>Evaluate:</strong> appraise, argue, defend, judge, select, support, value, evaluate</li>
                            <li><strong>Create:</strong> assemble, construct, create, design, develop, formulate, write</li>
                        </ul>
                    </div>
                </div>
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
        if (confirm('Are you sure you want to delete this Bloom\'s Taxonomy level? This action cannot be undone.')) {
            this.submit();
        }
    });
</script>
@endpush

