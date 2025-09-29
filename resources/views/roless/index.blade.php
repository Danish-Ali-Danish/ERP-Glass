@extends('layouts.master')
@section('content')
<div class="container-fluid px-4 py-4" style="max-width: 1200px;">
    <!-- ================= HEADER ================= -->
    <header class="mb-4">
        <h1 class="display-6 fw-bold text-dark">Roles & Permissions</h1>
        <p class="text-muted">Manage user roles and their permissions</p>
    </header>

    <!-- SUCCESS MESSAGE -->
    @if(session('success'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
                Swal.fire({
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 2000,
                    position: 'center',
                    timerProgressBar: true
                });
            });
    </script>
    @endif


    <div class="row g-4">
        <!-- ================= LEFT PANEL - ROLES LIST ================= -->
        <div class="col-12 col-lg-4">
            <div class="roles-container card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="h4 text-secondary mb-0">Roles</h2>
                    <button type="button" onclick="resetForm()" class="btn btn-primary d-flex align-items-center">
                        <i class="fas fa-plus me-2"></i> Add New Role
                    </button>
                </div>

                <div class="d-flex flex-column gap-3" id="rolesList">
                    @foreach($roles as $role)
                    <div class="role-card border rounded p-3" id="roleBtn{{ $role->id }}"
                        onclick="selectRole({{ $role->id }})">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <h3 class="h5 fw-semibold text-dark mb-1">{{ $role->name }}</h3>
                                <p class="small text-muted mb-0">{{ $role->permissions->count() }} permissions</p>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- DELETE ROLE -->
                                <form method="POST" action="{{ route('roles.destroy', $role) }}" class="delete-form">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-secondary fs-18 p-1"
                                        onclick="return confirm('Are you sure you want to delete this role?')">
                                        <i class="las la-trash"></i>
                                    </button>
                                </form>

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- ================= RIGHT PANEL ================= -->
        <div class="col-12 col-lg-8 card desktop-permissions">

            <!-- ================== VIEW ROLE ================== -->
            <div id="permissionsDisplay" class="p-4">
                <h2 class="h4 text-secondary mb-2" id="roleTitle">Select a Role</h2>
                <p class="mb-4">Select a role to view its permissions</p>
                <div class="mt-4 d-flex justify-content-end gap-3 p-3 border-top d-none" id="editBtnContainer">
                    <button type="button" onclick="openEdit()" class="btn btn-primary d-flex align-items-center">
                        <i class="las la-pen me-2"></i> Edit Permissions
                    </button>
                </div>

                <div id="permissionView" class="permission-view-empty">
                    <div class="text-center">
                        <i class="fas fa-shield-alt display-4 mb-3"></i>
                        <p>Select a role to view permissions</p>
                    </div>
                </div>
            </div>

            <!-- ================== ADD / EDIT ROLE FORM ================== -->
            <div id="roleFormContainer" class="permissions-display-container p-4 d-none">
                <h2 class="h4 text-secondary mb-2" id="formTitle">Add New Role</h2>
                <p class="text-muted small mb-4" id="formSubtitle">Create a new role and assign permissions</p>


                <form id="roleForm" method="POST">
                    <div class="d-flex justify-content-end gap-3 pt-3 border-top">
                        <button type="button" id="cancelBtn" class="btn btn-outline-secondary">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">Create Role</button>
                    </div>
                    @csrf
                    <input type="hidden" name="_method" id="formMethod" value="POST">
                    <input type="hidden" name="role_id" id="roleId">

                    <!-- ROLE NAME -->
                    <div class="mb-4">
                        <label for="roleName" class="form-label fw-medium text-dark">ROLE NAME</label>
                        <input type="text" id="roleName" name="name" class="form-control form-control-lg"
                            placeholder="Enter role name" required>
                    </div>

                    <!-- PERMISSIONS LIST -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h3 class="h5 fw-medium text-dark mb-0">Permissions</h3>
                            <button type="button" id="selectAllBtn" class="btn btn-sm btn-link text-blue-500 p-0">Select
                                All</button>
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

                    <!-- ACTION BUTTONS -->

                </form>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Permissions Modal -->
<div id="mobilePermissionsModal" class="modal mobile-permissions-modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content mobile-modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mobileModalTitle">Permissions</h5>
                <button type="button" class="btn-close" id="closeMobileModal"></button>
            </div>
            <div class="modal-body">
                <div id="mobilePermissionsContent">
                    <!-- Content will be dynamically inserted here -->
                </div>
            </div>
            <div class="modal-footer">
                <button id="mobileEditBtn" class="btn btn-primary-custom">
                    <i class="fas fa-edit me-2"></i> Edit
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Edit Permissions Modal -->
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
                        <button type="button" id="mobileSelectAllBtn"
                            class="btn btn-sm btn-link text-blue-500 p-0">Select All</button>
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
                                        id="mobile-perm-{{ $perm['id'] }}"
                                        class="mobile-perm-checkbox form-check-input permission-checkbox">
                                    <label for="mobile-perm-{{ $perm['id'] }}" class="form-check-label text-dark">{{
                                        $perm['name'] }}</label>
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

@endsection

@section('scripts')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Roles aur Permissions data Laravel se inject hua hai
    let roles = @json($rolesArray);
    let permissionGroups = @json($permissionGroups);
    let selectedRole = null; // abhi jo role select hoga uski reference

    // DOM Elements references
    const permissionsDisplay = document.getElementById('permissionsDisplay');
    const roleFormContainer = document.getElementById('roleFormContainer');
    const roleTitle = document.getElementById('roleTitle');
    const permissionView = document.getElementById('permissionView');
    const editBtnContainer = document.getElementById('editBtnContainer');
    const formTitle = document.getElementById('formTitle');
    const formSubtitle = document.getElementById('formSubtitle');
    const roleForm = document.getElementById('roleForm');
    const roleId = document.getElementById('roleId');
    const roleName = document.getElementById('roleName');
    const formMethod = document.getElementById('formMethod');
    const submitBtn = document.getElementById('submitBtn');
    
    // Mobile specific modal elements
    const mobilePermissionsModal = document.getElementById('mobilePermissionsModal');
    const mobileEditModal = document.getElementById('mobileEditModal');
    const mobileModalTitle = document.getElementById('mobileModalTitle');
    const mobileEditTitle = document.getElementById('mobileEditTitle');
    const mobilePermissionsContent = document.getElementById('mobilePermissionsContent');
    const mobilePermissionsList = document.getElementById('mobilePermissionsList');
    const mobileRoleName = document.getElementById('mobileRoleName');

    // Bootstrap modal instances
    let mobilePermissionsModalInstance;
    let mobileEditModalInstance;

    // Application initialization
    function init() {
        attachEventListeners(); // Event listeners attach kare
        // Bootstrap ke modals initialize kare
        mobilePermissionsModalInstance = new bootstrap.Modal(mobilePermissionsModal);
        mobileEditModalInstance = new bootstrap.Modal(mobileEditModal);
    }

    // Role select karne ka function (desktop + mobile)
    function selectRole(id) {
        selectedRole = roles.find(r => r.id === id); // role find kare
        roleTitle.textContent = selectedRole.name + " Permissions"; // title update
        renderPermissionsDisplay(); // permissions render kare
        editBtnContainer.classList.remove('d-none'); // edit button show kare

        // UI me selected role highlight kare
        document.querySelectorAll('.role-card').forEach(item => {
            item.classList.remove('selected');
        });
        document.getElementById('roleBtn' + id).classList.add('selected');

        // Agar screen mobile hai to modal open kare
        if (window.innerWidth < 1024) {
            showMobilePermissionsModal();
        }
    }

    // Mobile par role ke permissions modal me dikhaye
    function showMobilePermissionsModal() {
        if (!selectedRole) return;
        
        mobileModalTitle.textContent = `${selectedRole.name} Permissions`;
        
        let content = `
            <p class="text-muted small mb-3">${selectedRole.permissions.length} permissions assigned</p>
            <div class="d-flex flex-column gap-3">
        `;
        
        // Groups ke hisaab se permissions list banaye
        permissionGroups.forEach(group => {
            const groupPermissions = group.permissions.filter(p => 
                selectedRole.permissions.includes(p.id)
            );
            
            if (groupPermissions.length === 0) return;
            
            content += `
                <div class="permission-badge">
                    <h6 class="fw-medium text-dark mb-3">${group.name}</h6>
                    <ul class="list-unstyled mb-0">
                        ${groupPermissions.map(p => `
                            <li class="d-flex align-items-center mb-2">
                                <i class="fas fa-check-circle text-success-custom me-2"></i>
                                <span class="text-muted">${p.name}</span>
                            </li>
                        `).join('')}
                    </ul>
                </div>
            `;
        });
        
        content += `</div>`;
        mobilePermissionsContent.innerHTML = content;
        
        // Modal show kare
        mobilePermissionsModalInstance.show();
    }

    // Mobile par role edit karne ka modal show kare
    function showMobileEditModal() {
        if (!selectedRole) return;
        
        mobileEditTitle.textContent = `Edit ${selectedRole.name}`;
        mobileRoleName.value = selectedRole.name;
        
        // Permissions checkboxes render kare
        mobilePermissionsList.innerHTML = '';
        
        permissionGroups.forEach(group => {
            const groupElement = document.createElement('div');
            groupElement.className = 'permission-group border-bottom';
            
            const groupPermissions = group.permissions.map(permission => {
                const isChecked = selectedRole.permissions.includes(permission.id);
                
                return `
                    <div class="permission-item d-flex align-items-center p-2">
                        <input type="checkbox" id="mobile-perm-${permission.id}" 
                            data-permission="${permission.id}" 
                            ${isChecked ? 'checked' : ''}
                            class="mobile-perm-checkbox form-check-input permission-checkbox">
                        <label for="mobile-perm-${permission.id}" class="form-check-label text-dark">${permission.name}</label>
                    </div>
                `;
            }).join('');
            
            groupElement.innerHTML = `
                <div class="group-header p-3">
                    <h6 class="fw-medium text-dark mb-0">${group.name}</h6>
                </div>
                <div class="group-permissions permission-list-grid p-3">
                    ${groupPermissions}
                </div>
            `;
            
            mobilePermissionsList.appendChild(groupElement);
        });
        
        mobilePermissionsModalInstance.hide(); // old modal band kare
        mobileEditModalInstance.show(); // edit modal khole
    }

    // Mobile par edit ke baad save kare
    function saveMobileEditChanges() {
        if (!selectedRole) return;
        
        // Role ka naam update kare
        selectedRole.name = mobileRoleName.value;
        
        // Permissions update kare
        const checkboxes = mobilePermissionsList.querySelectorAll('.mobile-perm-checkbox');
        selectedRole.permissions = [];
        
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedRole.permissions.push(parseInt(checkbox.dataset.permission));
            }
        });
        
        // UI refresh kare aur notification show kare
        renderPermissionsDisplay();
        closeMobileEditModal();
        showNotification('Role updated successfully!');
    }

    // Modals close karne wale functions
    function closeMobileModal() {
        mobilePermissionsModalInstance.hide();
    }

    function closeMobileEditModal() {
        mobileEditModalInstance.hide();
    }

    // Desktop view me role ki permissions render kare
    // Desktop view me role ki permissions render kare
function renderPermissionsDisplay() {
    if (!selectedRole) return;

    let html = `<div class="row">`; // bootstrap row start

    permissionGroups.forEach((group, index) => {
        let groupHtml = "";
        group.permissions.forEach(p => {
            const has = selectedRole.permissions.includes(p.id);
            if (has) {
                groupHtml += `
                    <li class="d-flex align-items-center mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span class="text-muted">${p.name}</span>
                    </li>
                `;
            }
        });

        if (groupHtml) {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="permission-badge h-100 p-3 border rounded">
                        <h6 class="fw-medium text-dark mb-3">${group.name}</h6>
                        <ul class="list-unstyled mb-0">${groupHtml}</ul>
                    </div>
                </div>
            `;
        }
    });

    html += `</div>`; // row end

    // Agar koi permission assign na ho
    permissionView.innerHTML = html || `
        <div class="text-center">
            <i class="fas fa-shield-alt display-4 mb-3"></i>
            <p>No permissions assigned</p>
        </div>
    `;

    permissionView.classList.remove('permission-view-empty');
}

    // Desktop par role form show kare (add/edit)
    function showRoleForm(role = null) {
        if (role) {
            // Edit mode
            formTitle.textContent = 'Edit Role';
            formSubtitle.textContent = 'Modify role and its permissions';
            roleId.value = role.id;
            roleName.value = role.name;
            formMethod.value = 'PUT';
            roleForm.action = '/roles/' + role.id;
            submitBtn.textContent = 'Save Changes';
            selectedRole = role;
            
            // Permissions set kare
            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                cb.checked = selectedRole.permissions.includes(parseInt(cb.value));
            });
        } else {
            // Add mode
            formTitle.textContent = 'Add New Role';
            formSubtitle.textContent = 'Create a new role and assign permissions';
            roleForm.reset();
            formMethod.value = 'POST';
            roleForm.action = '/roles';
            submitBtn.textContent = 'Create Role';
            selectedRole = null;
            
            // Sab checkboxes clear kare
            document.querySelectorAll('.perm-checkbox').forEach(cb => {
                cb.checked = false;
            });
        }
        
        roleFormContainer.classList.remove('d-none');
        permissionsDisplay.classList.add('d-none');
    }

    // Desktop par form hide kare
    function hideRoleForm() {
        roleFormContainer.classList.add('d-none');
        permissionsDisplay.classList.remove('d-none');
        
        if (selectedRole) {
            renderPermissionsDisplay();
        } else {
            permissionView.innerHTML = `
                <div class="text-center">
                    <i class="fas fa-shield-alt display-4 mb-3"></i>
                    <p>Select a role to view permissions</p>
                </div>
            `;
            permissionView.classList.add('permission-view-empty');
            editBtnContainer.classList.add('d-none');
        }
    }

    // Naya role add karna ho
    function resetForm() {
        if (window.innerWidth < 1024) {
            // Mobile mode me edit modal show kare
            selectedRole = {
                id: Math.max(...roles.map(r => r.id), 0) + 1,
                name: "",
                permissions: []
            };
            showMobileEditModal();
        } else {
            showRoleForm();
        }
    }

    // Edit karna ho
    function openEdit() {
        if (selectedRole) {
            if (window.innerWidth < 1024) {
                showMobileEditModal();
            } else {
                showRoleForm(selectedRole);
            }
        }
    }

    // Edit cancel karna ho
    function cancelEdit() {
        if (window.innerWidth < 1024) {
            closeMobileEditModal();
            if (selectedRole) {
                showMobilePermissionsModal();
            } else {
                closeMobileModal();
            }
        } else {
            hideRoleForm();
        }
    }

    // Desktop par sabhi permissions select/deselect kare
    function selectAllPermissions() {
        const checkboxes = document.getElementById('permissionsList').querySelectorAll('.perm-checkbox');
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }

    // Mobile par sabhi permissions select/deselect kare
    function selectAllMobilePermissions() {
        const checkboxes = mobilePermissionsList.querySelectorAll('.mobile-perm-checkbox');
        const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        
        checkboxes.forEach(checkbox => {
            checkbox.checked = !allChecked;
        });
    }

    // Notification show karne ka function
    function showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'alert alert-success notification fade-in';
        notification.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // 3 sec baad fade out
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 500);
        }, 3000);
    }

    // Event listeners attach karna
    function attachEventListeners() {
        // Desktop form cancel
        document.getElementById('cancelBtn').addEventListener('click', hideRoleForm);
        
        // Desktop select all
        document.getElementById('selectAllBtn').addEventListener('click', selectAllPermissions);
        
        // Mobile modal buttons
        document.getElementById('closeMobileModal').addEventListener('click', closeMobileModal);
        document.getElementById('closeMobileEditModal').addEventListener('click', closeMobileEditModal);
        document.getElementById('mobileEditBtn').addEventListener('click', showMobileEditModal);
        document.getElementById('mobileCancelEditBtn').addEventListener('click', closeMobileEditModal);
        document.getElementById('mobileSaveBtn').addEventListener('click', saveMobileEditChanges);
        document.getElementById('mobileSelectAllBtn').addEventListener('click', selectAllMobilePermissions);
        
        // Window resize par desktop aur mobile state adjust karna
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024 && selectedRole) {
                closeMobileModal();
                closeMobileEditModal();
                renderPermissionsDisplay();
            }
        });

        // Form submit normal server side kare
        roleForm.addEventListener('submit', function(e) {
            // yahan JS handling ki zarurat nahi
        });

        // Delete role confirm dialog
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!confirm('Are you sure you want to delete this role?')) {
                    e.preventDefault();
                }
            });
        });
    }

    // DOM ready hone par init function call kare
    document.addEventListener('DOMContentLoaded', init);
</script>

@endsection