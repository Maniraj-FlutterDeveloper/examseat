@extends('layouts.app')

@section('title', 'Seating Plans')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chair mr-2"></i> Seating Plans
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-8">
                                <form action="{{ route('seating-plans.index') }}" method="GET" class="form-inline">
                                    <div class="form-group mr-2">
                                        <input type="text" name="search" class="form-control" placeholder="Search by title or exam code" value="{{ request('search') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <select name="status" class="form-control">
                                            <option value="">All Status</option>
                                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="date" name="date_from" class="form-control" placeholder="Date From" value="{{ request('date_from') }}">
                                    </div>
                                    <div class="form-group mr-2">
                                        <input type="date" name="date_to" class="form-control" placeholder="Date To" value="{{ request('date_to') }}">
                                    </div>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-filter mr-1"></i> Filter
                                    </button>
                                    <a href="{{ route('seating-plans.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-sync-alt mr-1"></i> Reset
                                    </a>
                                </form>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('seating-plans.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus-circle mr-1"></i> Create New Seating Plan
                                </a>
                            </div>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="seating-plans-table">
                            <thead class="thead-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Exam Date</th>
                                    <th>Time</th>
                                    <th>Rooms</th>
                                    <th>Students</th>
                                    <th>Status</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($seatingPlans as $plan)
                                    <tr>
                                        <td>{{ $plan->id }}</td>
                                        <td>
                                            <a href="{{ route('seating-plans.show', $plan) }}">
                                                {{ $plan->title }}
                                            </a>
                                            @if($plan->exam_code)
                                                <br>
                                                <small class="text-muted">{{ $plan->exam_code }}</small>
                                            @endif
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($plan->exam_date)->format('M d, Y') }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($plan->start_time)->format('h:i A') }} - 
                                            {{ \Carbon\Carbon::parse($plan->end_time)->format('h:i A') }}
                                        </td>
                                        <td>
                                            <span class="badge badge-info">{{ $plan->rooms_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge-primary">{{ $plan->students_count }}</span>
                                        </td>
                                        <td>
                                            @if($plan->status == 'draft')
                                                <span class="badge badge-secondary">Draft</span>
                                            @elseif($plan->status == 'published')
                                                <span class="badge badge-success">Published</span>
                                            @elseif($plan->status == 'completed')
                                                <span class="badge badge-info">Completed</span>
                                            @elseif($plan->status == 'cancelled')
                                                <span class="badge badge-danger">Cancelled</span>
                                            @endif
                                        </td>
                                        <td>{{ $plan->created_by_name }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('seating-plans.show', $plan) }}" class="btn btn-info btn-sm" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('seating-plans.edit', $plan) }}" class="btn btn-primary btn-sm" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('seating-plans.print', $plan) }}" class="btn btn-warning btn-sm" title="Print" target="_blank">
                                                    <i class="fas fa-print"></i>
                                                </a>
                                                <div class="btn-group" role="group">
                                                    <button id="actionDropdown{{ $plan->id }}" type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                        <i class="fas fa-cog"></i>
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="actionDropdown{{ $plan->id }}">
                                                        <a class="dropdown-item" href="{{ route('seating-plans.duplicate', $plan) }}">
                                                            <i class="fas fa-copy mr-1"></i> Duplicate
                                                        </a>
                                                        @if($plan->status == 'draft')
                                                            <form action="{{ route('seating-plans.publish', $plan) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-check-circle mr-1"></i> Publish
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($plan->status == 'published')
                                                            <form action="{{ route('seating-plans.complete', $plan) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="fas fa-flag-checkered mr-1"></i> Mark as Completed
                                                                </button>
                                                            </form>
                                                        @endif
                                                        @if($plan->status != 'cancelled')
                                                            <form action="{{ route('seating-plans.cancel', $plan) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                @method('PATCH')
                                                                <button type="submit" class="dropdown-item" onclick="return confirm('Are you sure you want to cancel this seating plan?');">
                                                                    <i class="fas fa-ban mr-1"></i> Cancel
                                                                </button>
                                                            </form>
                                                        @endif
                                                        <div class="dropdown-divider"></div>
                                                        <form action="{{ route('seating-plans.destroy', $plan) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Are you sure you want to delete this seating plan? This action cannot be undone.');">
                                                                <i class="fas fa-trash mr-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">No seating plans found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $seatingPlans->appends(request()->except('page'))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#seating-plans-table').DataTable({
            "paging": false,
            "info": false,
            "responsive": true,
            "language": {
                "search": "Quick search:",
                "zeroRecords": "No matching seating plans found"
            }
        });
    });
</script>
@endsection

