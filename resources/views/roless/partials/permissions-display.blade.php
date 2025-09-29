<div id="permissionsDisplay" class="permissions-display-container p-4">
    <h2 class="h4 text-secondary mb-2" id="roleTitle">Select a Role</h2>
    <p class="text-muted small mb-4">Select a role to view its permissions</p>
    
    <div id="permissionView" class="permission-view-empty">
        <div class="text-center">
            <i class="fas fa-shield-alt display-4 mb-3"></i>
            <p>Select a role to view permissions</p>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-end gap-3 pt-3 border-top d-none" id="editBtnContainer">
        <button onclick="openEdit()" class="btn btn-primary-custom">
            <i class="fas fa-edit me-2"></i> Edit Permissions
        </button>
    </div>
</div>
