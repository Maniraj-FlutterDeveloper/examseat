@extends('layouts.admin')

@section('title', 'Analytics Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/gridstack@7.2.3/dist/gridstack.min.css" />
<style>
    .grid-stack-item-content {
        padding: 10px;
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .widget-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
        margin-bottom: 10px;
    }
    
    .widget-title {
        font-weight: 600;
        margin: 0;
    }
    
    .widget-actions .dropdown-toggle::after {
        display: none;
    }
    
    .widget-body {
        height: calc(100% - 50px);
        overflow: auto;
    }
    
    .metric-widget {
        text-align: center;
        padding: 20px;
    }
    
    .metric-icon {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .metric-label {
        font-size: 1rem;
        color: #6c757d;
    }
    
    .chart-container {
        width: 100%;
        height: 100%;
        min-height: 200px;
    }
    
    .table-widget {
        height: 100%;
        overflow: auto;
    }
    
    .list-widget {
        height: 100%;
        overflow: auto;
    }
    
    .list-widget .list-group-item {
        border-left: none;
        border-right: none;
    }
    
    .list-widget .list-group-item:first-child {
        border-top: none;
    }
    
    .list-widget .list-group-item:last-child {
        border-bottom: none;
    }
    
    .dashboard-toolbar {
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Analytics Dashboard</h1>
        <div>
            <button class="btn btn-primary me-2" id="save-dashboard">
                <i class="fas fa-save me-1"></i> Save Layout
            </button>
            <div class="btn-group">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-cog me-1"></i> Dashboard Options
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('admin.analytics.create_dashboard') }}"><i class="fas fa-plus-circle me-2"></i> New Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.analytics.edit_dashboard', $dashboard->id) }}"><i class="fas fa-edit me-2"></i> Edit Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin.analytics.create_widget', $dashboard->id) }}"><i class="fas fa-chart-bar me-2"></i> Add Widget</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{{ route('admin.analytics.destroy_dashboard', $dashboard->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this dashboard?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> Delete Dashboard</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Dashboard Selector -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <label for="dashboard-select" class="me-2 mb-0">Dashboard:</label>
                        <select id="dashboard-select" class="form-select">
                            @foreach($dashboards as $d)
                                <option value="{{ $d->id }}" {{ $dashboard->id == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" id="refresh-all">
                            <i class="fas fa-sync-alt me-1"></i> Refresh All
                        </button>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-columns me-1"></i> Layout
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#" data-columns="1"><i class="fas fa-grip-vertical me-2"></i> 1 Column</a></li>
                                <li><a class="dropdown-item" href="#" data-columns="2"><i class="fas fa-grip-horizontal me-2"></i> 2 Columns</a></li>
                                <li><a class="dropdown-item" href="#" data-columns="3"><i class="fas fa-th-large me-2"></i> 3 Columns</a></li>
                                <li><a class="dropdown-item" href="#" data-columns="4"><i class="fas fa-th me-2"></i> 4 Columns</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grid Stack Container -->
    <div class="grid-stack"></div>
    
    <!-- Empty State -->
    @if($widgets->isEmpty())
        <div class="text-center py-5" id="empty-state">
            <div class="mb-4">
                <i class="fas fa-chart-line fa-4x text-muted"></i>
            </div>
            <h4>No widgets found</h4>
            <p class="text-muted">Add widgets to your dashboard to start analyzing your data.</p>
            <a href="{{ route('admin.analytics.create_widget', $dashboard->id) }}" class="btn btn-primary mt-2">
                <i class="fas fa-plus-circle me-1"></i> Add Widget
            </a>
        </div>
    @endif
</div>

<!-- Widget Templates -->
<template id="metric-widget-template">
    <div class="widget-header">
        <h5 class="widget-title">{title}</h5>
        <div class="widget-actions">
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item refresh-widget" href="#"><i class="fas fa-sync-alt me-2"></i> Refresh</a></li>
                    <li><a class="dropdown-item" href="{edit_url}"><i class="fas fa-edit me-2"></i> Edit</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{delete_url}" method="POST" class="delete-widget-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> Delete</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="widget-body">
        <div class="metric-widget">
            <div class="metric-icon text-{color}">
                <i class="fas fa-{icon}"></i>
            </div>
            <div class="metric-value">{value}</div>
            <div class="metric-label">{label}</div>
        </div>
    </div>
</template>

<template id="chart-widget-template">
    <div class="widget-header">
        <h5 class="widget-title">{title}</h5>
        <div class="widget-actions">
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item refresh-widget" href="#"><i class="fas fa-sync-alt me-2"></i> Refresh</a></li>
                    <li><a class="dropdown-item" href="{edit_url}"><i class="fas fa-edit me-2"></i> Edit</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{delete_url}" method="POST" class="delete-widget-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> Delete</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="widget-body">
        <div class="chart-container">
            <canvas></canvas>
        </div>
    </div>
</template>

<template id="table-widget-template">
    <div class="widget-header">
        <h5 class="widget-title">{title}</h5>
        <div class="widget-actions">
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item refresh-widget" href="#"><i class="fas fa-sync-alt me-2"></i> Refresh</a></li>
                    <li><a class="dropdown-item" href="{edit_url}"><i class="fas fa-edit me-2"></i> Edit</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{delete_url}" method="POST" class="delete-widget-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> Delete</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="widget-body">
        <div class="table-widget">
            <table class="table table-sm table-hover mb-0">
                <thead>
                    <tr>
                        {headers}
                    </tr>
                </thead>
                <tbody>
                    {rows}
                </tbody>
            </table>
        </div>
    </div>
</template>

<template id="list-widget-template">
    <div class="widget-header">
        <h5 class="widget-title">{title}</h5>
        <div class="widget-actions">
            <div class="dropdown">
                <button class="btn btn-sm btn-link text-muted p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item refresh-widget" href="#"><i class="fas fa-sync-alt me-2"></i> Refresh</a></li>
                    <li><a class="dropdown-item" href="{edit_url}"><i class="fas fa-edit me-2"></i> Edit</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form action="{delete_url}" method="POST" class="delete-widget-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger"><i class="fas fa-trash-alt me-2"></i> Delete</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="widget-body">
        <div class="list-widget">
            <ul class="list-group list-group-flush">
                {items}
            </ul>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/gridstack@7.2.3/dist/gridstack-all.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize GridStack
        const grid = GridStack.init({
            column: {{ $dashboard->layout['columns'] ?? 3 }},
            cellHeight: 100,
            margin: 10,
            disableOneColumnMode: false,
            float: true,
            handle: '.widget-header',
            resizable: {
                handles: 'e, se, s, sw, w'
            }
        });
        
        // Widget data
        const widgetData = @json($widgetData);
        
        // Chart instances
        const chartInstances = {};
        
        // Add widgets to the grid
        @foreach($widgets as $widget)
            grid.addWidget({
                x: {{ $widget->position['x'] ?? 'null' }},
                y: {{ $widget->position['y'] ?? 'null' }},
                w: {{ $widget->position['w'] ?? ($widget->size === 'small' ? 1 : ($widget->size === 'medium' ? 2 : ($widget->size === 'large' ? 3 : 4))) }},
                h: {{ $widget->position['h'] ?? ($widget->size === 'small' ? 2 : ($widget->size === 'medium' ? 3 : ($widget->size === 'large' ? 4 : 5))) }},
                id: '{{ $widget->id }}',
                content: getWidgetContent('{{ $widget->type }}', '{{ $widget->id }}', '{{ $widget->title }}')
            });
            
            // Initialize widget data
            initializeWidget('{{ $widget->type }}', '{{ $widget->id }}', widgetData['{{ $widget->id }}']);
            
            // Set up refresh interval if needed
            @if($widget->refresh_interval > 0)
                setInterval(function() {
                    refreshWidget('{{ $widget->id }}');
                }, {{ $widget->refresh_interval * 1000 }});
            @endif
        @endforeach
        
        // Hide empty state if widgets exist
        if (grid.getGridItems().length > 0) {
            document.getElementById('empty-state')?.classList.add('d-none');
        }
        
        // Save dashboard layout
        document.getElementById('save-dashboard').addEventListener('click', function() {
            const items = grid.getGridItems();
            const positions = [];
            
            items.forEach(item => {
                const node = item.gridstackNode;
                positions.push({
                    id: node.id,
                    position: {
                        x: node.x,
                        y: node.y,
                        w: node.w,
                        h: node.h
                    }
                });
            });
            
            fetch('{{ route('admin.analytics.update_widget_positions', $dashboard->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ positions: positions })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Dashboard layout saved successfully', 'success');
                } else {
                    showToast('Failed to save dashboard layout', 'error');
                }
            })
            .catch(error => {
                console.error('Error saving dashboard layout:', error);
                showToast('Failed to save dashboard layout', 'error');
            });
        });
        
        // Dashboard selector
        document.getElementById('dashboard-select').addEventListener('change', function() {
            const dashboardId = this.value;
            window.location.href = '{{ route('admin.analytics.dashboard') }}/' + dashboardId;
        });
        
        // Column layout selector
        document.querySelectorAll('[data-columns]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const columns = parseInt(this.getAttribute('data-columns'));
                grid.column(columns);
                showToast(`Layout changed to ${columns} columns`, 'info');
            });
        });
        
        // Refresh all widgets
        document.getElementById('refresh-all').addEventListener('click', function() {
            const items = grid.getGridItems();
            items.forEach(item => {
                const widgetId = item.gridstackNode.id;
                refreshWidget(widgetId);
            });
            showToast('Refreshing all widgets', 'info');
        });
        
        // Refresh individual widget
        document.addEventListener('click', function(e) {
            if (e.target.closest('.refresh-widget')) {
                e.preventDefault();
                const widgetElement = e.target.closest('.grid-stack-item');
                const widgetId = widgetElement.getAttribute('gs-id');
                refreshWidget(widgetId);
            }
        });
        
        // Delete widget confirmation
        document.addEventListener('submit', function(e) {
            if (e.target.classList.contains('delete-widget-form')) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this widget?')) {
                    e.target.submit();
                }
            }
        });
        
        // Helper function to get widget content
        function getWidgetContent(type, id, title) {
            let template;
            
            switch (type) {
                case 'metric':
                    template = document.getElementById('metric-widget-template').innerHTML;
                    break;
                case 'chart':
                    template = document.getElementById('chart-widget-template').innerHTML;
                    break;
                case 'table':
                    template = document.getElementById('table-widget-template').innerHTML;
                    break;
                case 'list':
                    template = document.getElementById('list-widget-template').innerHTML;
                    break;
                default:
                    return `<div class="p-3">Unsupported widget type: ${type}</div>`;
            }
            
            return template
                .replace(/{title}/g, title)
                .replace(/{edit_url}/g, '{{ route('admin.analytics.edit_widget', '') }}/' + id)
                .replace(/{delete_url}/g, '{{ route('admin.analytics.destroy_widget', '') }}/' + id);
        }
        
        // Initialize widget with data
        function initializeWidget(type, id, data) {
            const widgetElement = document.querySelector(`.grid-stack-item[gs-id="${id}"]`);
            
            if (!widgetElement) return;
            
            if (data.error) {
                widgetElement.querySelector('.widget-body').innerHTML = `
                    <div class="alert alert-danger m-3">
                        <i class="fas fa-exclamation-circle me-2"></i> ${data.error}
                    </div>
                `;
                return;
            }
            
            switch (type) {
                case 'metric':
                    initializeMetricWidget(widgetElement, data);
                    break;
                case 'chart':
                    initializeChartWidget(widgetElement, id, data);
                    break;
                case 'table':
                    initializeTableWidget(widgetElement, data);
                    break;
                case 'list':
                    initializeListWidget(widgetElement, data);
                    break;
            }
        }
        
        // Initialize metric widget
        function initializeMetricWidget(element, data) {
            const metricWidget = element.querySelector('.metric-widget');
            metricWidget.querySelector('.metric-icon').className = `metric-icon text-${data.color}`;
            metricWidget.querySelector('.metric-icon i').className = `fas fa-${data.icon}`;
            metricWidget.querySelector('.metric-value').textContent = data.value;
            metricWidget.querySelector('.metric-label').textContent = data.label;
        }
        
        // Initialize chart widget
        function initializeChartWidget(element, id, data) {
            const canvas = element.querySelector('canvas');
            
            // Destroy existing chart if it exists
            if (chartInstances[id]) {
                chartInstances[id].destroy();
            }
            
            // Create new chart
            chartInstances[id] = new Chart(canvas, {
                type: data.type,
                data: data.data,
                options: data.options
            });
        }
        
        // Initialize table widget
        function initializeTableWidget(element, data) {
            const tableWidget = element.querySelector('.table-widget');
            const table = tableWidget.querySelector('table');
            
            // Create headers
            let headerHtml = '';
            data.headers.forEach(header => {
                headerHtml += `<th>${header}</th>`;
            });
            table.querySelector('thead tr').innerHTML = headerHtml;
            
            // Create rows
            let rowsHtml = '';
            data.rows.forEach(row => {
                let rowHtml = '<tr>';
                row.forEach(cell => {
                    rowHtml += `<td>${cell}</td>`;
                });
                rowHtml += '</tr>';
                rowsHtml += rowHtml;
            });
            table.querySelector('tbody').innerHTML = rowsHtml;
        }
        
        // Initialize list widget
        function initializeListWidget(element, data) {
            const listWidget = element.querySelector('.list-widget');
            const list = listWidget.querySelector('ul');
            
            let itemsHtml = '';
            data.items.forEach(item => {
                itemsHtml += `
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold">${item.title}</div>
                                <div class="small text-muted">${item.subtitle}</div>
                            </div>
                            <small class="text-muted">${formatDate(item.timestamp)}</small>
                        </div>
                        ${item.description ? `<p class="mb-0 mt-1 small">${item.description}</p>` : ''}
                        ${item.link ? `<a href="${item.link}" class="stretched-link"></a>` : ''}
                    </li>
                `;
            });
            list.innerHTML = itemsHtml;
        }
        
        // Refresh widget data
        function refreshWidget(id) {
            const widgetElement = document.querySelector(`.grid-stack-item[gs-id="${id}"]`);
            
            if (!widgetElement) return;
            
            // Show loading indicator
            const widgetBody = widgetElement.querySelector('.widget-body');
            const originalContent = widgetBody.innerHTML;
            widgetBody.innerHTML = `
                <div class="d-flex justify-content-center align-items-center h-100">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;
            
            // Fetch updated data
            fetch(`{{ route('admin.analytics.widget_data', '') }}/${id}`)
                .then(response => response.json())
                .then(data => {
                    // Restore original content first
                    widgetBody.innerHTML = originalContent;
                    
                    // Get widget type
                    const widgetType = widgetElement.querySelector('.widget-body > div').className.split('-')[0].trim();
                    
                    // Update widget with new data
                    initializeWidget(widgetType, id, data);
                })
                .catch(error => {
                    console.error('Error refreshing widget:', error);
                    widgetBody.innerHTML = `
                        <div class="alert alert-danger m-3">
                            <i class="fas fa-exclamation-circle me-2"></i> Failed to refresh widget
                        </div>
                    `;
                });
        }
        
        // Format date helper
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
        
        // Show toast notification
        function showToast(message, type = 'success') {
            // Create toast container if it doesn't exist
            let toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast
            const toastId = 'toast-' + Date.now();
            const toast = document.createElement('div');
            toast.className = `toast align-items-center text-white bg-${type} border-0`;
            toast.setAttribute('id', toastId);
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');
            
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            toastContainer.appendChild(toast);
            
            // Show toast
            const bsToast = new bootstrap.Toast(toast, {
                autohide: true,
                delay: 3000
            });
            bsToast.show();
            
            // Remove toast after it's hidden
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }
    });
</script>
@endpush

