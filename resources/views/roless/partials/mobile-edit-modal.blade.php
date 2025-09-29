<div id="mobileEditModal" class="modal mobile-permissions-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content mobile-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mobileEditTitle">Edit Permissions</h5>
                <button type="button" class="btn-close" id="closeMobileEditModal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="mobileRoleName" class="form-label fw-medium text-dark">ROLE NAME</label>
                    <input type="text" id="mobileRoleName" class="form-control" placeholder="Enter role name" required>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-medium text-dark mb-0">Permissions</h6>
                        <button type="button" id="mobileSelectAllBtn" class="btn btn-sm btn-link text-blue-500 p-0">Select All</button>
                    </div>
                    
                    <div class="permission-group-border" id="mobilePermissionsList">
                        @foreach($permissionGroups as $group)
                            <div class="permission-group border-bottom">
                                <div class="group-header p-3">
                                    <h6 class="fw-medium text-dark mb-0">{{ $group['name'] }}</h6>
                                </div>
                                <div class="group-permissions permission-list-grid p-3">
                                    @foreach($group['permissions'] as $perm)
                                        <div class="permission-item d-flex align-items-center p-2">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm['id'] }}" 
                                                   id="mobile-perm-{{ $perm['id'] }}" class="mobile-perm-checkbox form-check-input permission-checkbox">
                                            <label for="mobile-perm-{{ $perm['id'] }}" class="form-check-label text-dark">{{ $perm['name'] }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button id="mobileCancelEditBtn" class="btn btn-outline-secondary">Cancel</button>
                <button id="mobileSaveBtn" class="btn btn-primary-custom">Save Changes</button>
            </div>
        </div>
    </div>
</div>
