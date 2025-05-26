@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Create New Role</h5>
                    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back to Roles
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Role Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                                    <small class="form-text text-muted">Unique identifier for the role (e.g., admin, editor)</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="display_name">Display Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="display_name" name="display_name" value="{{ old('display_name') }}" required>
                                    <small class="form-text text-muted">Human-readable name (e.g., Administrator, Editor)</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group mb-4">
                            <label>Permissions <span class="text-danger">*</span></label>
                            <div class="alert alert-info">
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                    <label class="form-check-label" for="select-all">
                                        <strong>Select All Permissions</strong>
                                    </label>
                                </div>
                            </div>

                            <div class="row">
                                @foreach ($permissions as $module => $modulePermissions)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-header bg-light">
                                                <div class="form-check">
                                                    <input class="form-check-input module-checkbox" type="checkbox" id="module-{{ $module }}">
                                                    <label class="form-check-label" for="module-{{ $module }}">
                                                        <strong>{{ ucwords(str_replace('_', ' ', $module)) }}</strong>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                @foreach ($modulePermissions as $permission)
                                                    <div class="form-check">
                                                        <input class="form-check-input permission-checkbox" type="checkbox" 
                                                            name="permissions[]" 
                                                            value="{{ $permission->id }}" 
                                                            id="permission-{{ $permission->id }}"
                                                            data-module="{{ $module }}"
                                                            {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="permission-{{ $permission->id }}">
                                                            {{ $permission->display_name }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Select All checkbox
        const selectAllCheckbox = document.getElementById('select-all');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');
        
        selectAllCheckbox.addEventListener('change', function() {
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCheckbox.checked;
            });
            
            // Also update module checkboxes
            document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
                moduleCheckbox.checked = selectAllCheckbox.checked;
            });
        });
        
        // Module checkboxes
        document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
            moduleCheckbox.addEventListener('change', function() {
                const module = this.id.replace('module-', '');
                document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`).forEach(checkbox => {
                    checkbox.checked = moduleCheckbox.checked;
                });
                
                updateSelectAllCheckbox();
            });
        });
        
        // Individual permission checkboxes
        permissionCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const module = this.getAttribute('data-module');
                const moduleCheckbox = document.getElementById(`module-${module}`);
                const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                
                // Check if all permissions in this module are checked
                const allChecked = Array.from(modulePermissions).every(p => p.checked);
                moduleCheckbox.checked = allChecked;
                
                updateSelectAllCheckbox();
            });
        });
        
        function updateSelectAllCheckbox() {
            selectAllCheckbox.checked = Array.from(permissionCheckboxes).every(p => p.checked);
        }
        
        // Initial state
        document.querySelectorAll('.module-checkbox').forEach(moduleCheckbox => {
            const module = moduleCheckbox.id.replace('module-', '');
            const modulePermissions = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
            const allChecked = Array.from(modulePermissions).every(p => p.checked);
            moduleCheckbox.checked = allChecked;
        });
        
        updateSelectAllCheckbox();
    });
</script>
@endpush
@endsection

