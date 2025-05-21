@extends('layouts.admin')

@section('title', 'Reports')

@section('styles')
<style>
    .report-card {
        transition: transform 0.3s;
    }
    
    .report-card:hover {
        transform: translateY(-5px);
    }
    
    .favorite-icon {
        color: #ccc;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .favorite-icon.active {
        color: #ffc107;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Reports</h1>
        <a href="{{ route('admin.reports.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Create New Report
        </a>
    </div>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filters</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label for="type" class="form-label">Report Type</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">All Types</option>
                        @foreach($types as $key => $value)
                            <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="favorite" class="form-label">Favorites</label>
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="favorite" id="favorite" value="1" {{ request('favorite') ? 'checked' : '' }}>
                        <label class="form-check-label" for="favorite">
                            Show only favorites
                        </label>
                    </div>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter me-2"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Reports List -->
    <div class="row">
        @if($reports->count() > 0)
            @foreach($reports as $report)
                <div class="col-md-4 mb-4">
                    <div class="card report-card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">{{ $report->name }}</h5>
                            <form action="{{ route('admin.reports.toggle_favorite', $report->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-link p-0">
                                    <i class="fas fa-star favorite-icon {{ $report->is_favorite ? 'active' : '' }}"></i>
                                </button>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <span class="badge bg-primary">{{ $types[$report->type] ?? $report->type }}</span>
                                @if($report->last_generated_at)
                                    <span class="badge bg-info">Last Generated: {{ $report->last_generated_at->diffForHumans() }}</span>
                                @endif
                            </div>
                            
                            <p class="card-text">{{ $report->description ?? 'No description available.' }}</p>
                            
                            @if($report->latestResult && $report->latestResult->isSuccessful())
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i> Last generation was successful
                                    @if($report->latestResult->hasFile())
                                        <a href="{{ route('admin.reports.download', $report->latestResult->id) }}" class="alert-link ms-2">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                    @endif
                                </div>
                            @elseif($report->latestResult && !$report->latestResult->isSuccessful())
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i> Last generation failed
                                </div>
                            @endif
                        </div>
                        <div class="card-footer d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.reports.show', $report->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-eye me-1"></i> View
                                </a>
                                <a href="{{ route('admin.reports.edit', $report->id) }}" class="btn btn-sm btn-secondary">
                                    <i class="fas fa-edit me-1"></i> Edit
                                </a>
                            </div>
                            <form action="{{ route('admin.reports.generate', $report->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">
                                    <i class="fas fa-sync-alt me-1"></i> Generate
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i> No reports found. <a href="{{ route('admin.reports.create') }}" class="alert-link">Create your first report</a>.
                </div>
            </div>
        @endif
    </div>
    
    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $reports->appends(request()->query())->links() }}
    </div>
</div>
@endsection

