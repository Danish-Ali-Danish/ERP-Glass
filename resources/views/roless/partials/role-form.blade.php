<div id="roleFormContainer" class="permissions-display-container p-4 d-none">
    <h2 class="h4 text-secondary mb-2" id="formTitle">Add New Role</h2>
    <p class="text-muted small mb-4" id="formSubtitle">Create a new role and assign permissions</p>
    
    <form id="roleForm" method="POST">
        @csrf
        <input type="hidden" name="_method" id="formMethod" value="POST">
        <input type="hidden" name="role_id" id="roleId">
        
        <div class="mb-4">
            <label for="roleName" class="form-label fw-medium text-dark">ROLE NAME</label>
            <input type="text" id="roleName" name="name" class="form-control form-control-lg" placeholder="Enter role name" required>
        </div>
        
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h3 class="h5 fw-medium text-dark mb-0">Permissions</h3>
                <button type="button" id="selectAllBtn" class="btn btn-sm btn-link text-blue-500 p-0">Select All</button>
            </div>
            
            <div class="permission-group-border" id="permissionsList">
                @foreach($permissionGroups as $group)
                    <div class="permission-group border-bottom">
                        <div class="group-header p-3">
                            <h3 class="fw-medium text-dark mb-0">{{ $group['name'] }}</h3>
                        </div>
                        <div class="group-permissions permission-list-grid p-3">
                            @foreach($group['permissions'] as $perm)
                                <div class="permission-item d-flex align-items-center p-2 rounded border">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm['id'] }}" 
                                        id="perm-{{ $perm['id'] }}" 
                                        class="perm-checkbox form-check-input permission-checkbox">
                                    <label for="perm-{{ $perm['id'] }}" class="form-check-label text-dark">
                                        {{ $perm['name'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <div class="d-flex justify-content-end gap-3 pt-3 border-top">
            <button type="button" id="cancelBtn" class="btn btn-outline-secondary">Cancel</button>
            <button type="submit" class="btn btn-primary-custom" id="submitBtn">Create Role</button>
        </div>
    </form>
</div>
