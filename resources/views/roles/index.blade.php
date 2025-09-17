<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roles & Permissions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .permission-group {
            transition: all 0.3s ease;
        }
        .permission-item {
            transition: background-color 0.2s ease;
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        .role-card {
            transition: all 0.2s ease;
        }
        .role-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @media (max-width: 1023px) {
            .desktop-permissions {
                display: none;
            }
        }
        @media (min-width: 1024px) {
            .mobile-permissions-modal {
                display: none;
            }
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 py-8 max-w-6xl">
        <!-- Header -->
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Roles & Permissions</h1>
            <p class="text-gray-600">Manage user roles and their permissions</p>
        </header>

        @if(session('success'))
            <div class="bg-green-500 text-white px-4 py-3 rounded-lg mb-6 fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Panel - Roles List -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-semibold text-gray-700">Roles</h2>
                        <button type="button" onclick="resetForm()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i> Add New Role
                        </button>
                    </div>
                    
                    <div class="space-y-4" id="rolesList">
                        @foreach($roles as $role)
                            <div class="role-card border border-gray-200 rounded-lg p-4 cursor-pointer transition-all duration-200" 
                                 id="roleBtn{{ $role->id }}" onclick="selectRole({{ $role->id }})">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="font-semibold text-gray-800 text-lg">{{ $role->name }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ $role->permissions->count() }} permissions</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <form method="POST" action="{{ route('roles.destroy', $role) }}" class="delete-form">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="text-red-500 hover:text-red-700 p-1" onclick="return confirm('Are you sure you want to delete this role?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Panel - Desktop Permissions -->
            <div class="w-full lg:w-2/3  desktop-permissions">
                <!-- Permissions Display Container (initially shown) -->
                <div id="permissionsDisplay" class="bg-white rounded-xl shadow-sm p-6">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2" id="roleTitle">Select a Role</h2>
                    <p class="text-gray-500 text-sm mb-6">Select a role to view its permissions</p>
                    
                    <div id="permissionView" class="flex items-center grid grid-cols-2 gap-3 justify-center h-64 text-gray-400">
                        <div class="text-center">
                            <i class="fas fa-shield-alt text-4xl mb-3"></i>
                            <p>Select a role to view permissions</p>
                        </div>
                    </div>

                    <!-- Edit Button -->
                    <div class="mt-8 flex justify-end space-x-3 pt-4 border-t border-gray-200 hidden" id="editBtnContainer">
                        <button onclick="openEdit()" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium">
                            <i class="fas fa-edit mr-2"></i> Edit Permissions
                        </button>
                    </div>
                </div>

                <!-- Role Form Container (initially hidden) -->
                <div id="roleFormContainer" class="bg-white rounded-xl shadow-sm p-6 hidden">
                    <h2 class="text-xl font-semibold text-gray-700 mb-2" id="formTitle">Add New Role</h2>
                    <p class="text-gray-500 text-sm mb-6" id="formSubtitle">Create a new role and assign permissions</p>
                    
                    <form id="roleForm" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <input type="hidden" name="role_id" id="roleId">
                        
                        <div class="mb-6">
                            <label for="roleName" class="block text-gray-700 mb-2 font-medium">ROLE NAME</label>
                            <input type="text" id="roleName" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter role name" required>
                        </div>
                        
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium text-gray-700">Permissions</h3>
                                <button type="button" id="selectAllBtn" class="text-blue-500 text-sm font-medium">Select All</button>
                            </div>
                            
                            <div class="border border-gray-200 rounded-lg divide-y" id="permissionsList">
                                @foreach($permissionGroups as $group)
                                    <div class="permission-group">
                                        <div class="group-header p-3 bg-gray-50">
                                            <h3 class="font-medium text-gray-700">{{ $group['name'] }}</h3>
                                        </div>
                                      <div class="group-permissions grid grid-cols-2 gap-3">
                                            @foreach($group['permissions'] as $perm)
                                                <div class="permission-item flex items-center p-3 hover:bg-gray-50 rounded border">
                                                    <input type="checkbox" name="permissions[]" value="{{ $perm['id'] }}" 
                                                        id="perm-{{ $perm['id'] }}" 
                                                        class="perm-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                                                    <label for="perm-{{ $perm['id'] }}" class="ml-2 text-gray-700">
                                                        {{ $perm['name'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                            <button type="button" id="cancelBtn" class="px-5 py-2.5 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-5 py-2.5 rounded-lg font-medium" id="submitBtn">Create Role</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Permissions Modal -->
    <div id="mobilePermissionsModal" class="mobile-permissions-modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="mobileModalTitle" class="text-xl font-semibold text-gray-800">Permissions</h3>
                    <button id="closeMobileModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div id="mobilePermissionsContent">
                    <!-- Content will be dynamically inserted here -->
                </div>
                
                <div class="mt-6 flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button id="mobileEditBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-edit mr-2"></i> Edit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Edit Permissions Modal -->
    <div id="mobileEditModal" class="mobile-permissions-modal fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50 hidden">
        <div class="bg-white rounded-xl w-full max-w-md max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 id="mobileEditTitle" class="text-xl font-semibold text-gray-800">Edit Permissions</h3>
                    <button id="closeMobileEditModal" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <label for="mobileRoleName" class="block text-gray-700 mb-2 font-medium">ROLE NAME</label>
                    <input type="text" id="mobileRoleName" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Enter role name" required>
                </div>
                
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-medium text-gray-700">Permissions</h3>
                        <button type="button" id="mobileSelectAllBtn" class="text-blue-500 text-sm font-medium">Select All</button>
                    </div>
                    
                    <div class="border border-gray-200 rounded-lg divide-y" id="mobilePermissionsList">
                        @foreach($permissionGroups as $group)
                            <div class="permission-group">
                                <div class="group-header p-3 bg-gray-50">
                                    <h3 class="font-medium text-gray-700">{{ $group['name'] }}</h3>
                                </div>
                                <div class="group-permissions grid grid-cols-2 gap-3">
                                    @foreach($group['permissions'] as $perm)
                                        <div class="permission-item flex items-center p-3 hover:bg-gray-50">
                                            <input type="checkbox" name="permissions[]" value="{{ $perm['id'] }}" 
                                                   id="mobile-perm-{{ $perm['id'] }}" class="mobile-perm-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                                            <label for="mobile-perm-{{ $perm['id'] }}" class="ml-2 text-gray-700">{{ $perm['name'] }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button id="mobileCancelEditBtn" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium">Cancel</button>
                    <button id="mobileSaveBtn" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg font-medium">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Sample data structure
        let roles = @json($rolesArray);
        let permissionGroups = @json($permissionGroups);
        let selectedRole = null;

        // DOM Elements
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
        
        // Mobile modal elements
        const mobilePermissionsModal = document.getElementById('mobilePermissionsModal');
        const mobileEditModal = document.getElementById('mobileEditModal');
        const mobileModalTitle = document.getElementById('mobileModalTitle');
        const mobileEditTitle = document.getElementById('mobileEditTitle');
        const mobilePermissionsContent = document.getElementById('mobilePermissionsContent');
        const mobilePermissionsList = document.getElementById('mobilePermissionsList');
        const mobileRoleName = document.getElementById('mobileRoleName');

        // Initialize the application
        function init() {
            attachEventListeners();
        }

        // Select a role to view permissions
        function selectRole(id) {
            selectedRole = roles.find(r => r.id === id);
            roleTitle.textContent = selectedRole.name + " Permissions";
            renderPermissionsDisplay();
            editBtnContainer.classList.remove('hidden');

            // Highlight selected role
            document.querySelectorAll('.role-card').forEach(item => {
                item.classList.remove('border-blue-500', 'bg-blue-50');
            });
            document.getElementById('roleBtn' + id).classList.add('border-blue-500', 'bg-blue-50');
        }

        // Show mobile permissions modal
        function showMobilePermissionsModal() {
            if (!selectedRole) return;
            
            mobileModalTitle.textContent = `${selectedRole.name} Permissions`;
            
            let content = `
                <p class="text-gray-500 text-sm mb-4">${selectedRole.permissions.length} permissions assigned</p>
                <div class="space-y-4">
            `;
            
            permissionGroups.forEach(group => {
                const groupPermissions = group.permissions.filter(p => 
                    selectedRole.permissions.includes(p.id)
                );
                
                if (groupPermissions.length === 0) return;
                
                content += `
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h3 class="font-medium text-gray-700 mb-3">${group.name}</h3>
                        <ul class="space-y-2">
                            ${groupPermissions.map(p => `
                                <li class="flex items-center">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                    <span class="text-gray-600">${p.name}</span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            });
            
            content += `</div>`;
            mobilePermissionsContent.innerHTML = content;
            
            mobilePermissionsModal.classList.remove('hidden');
        }

        // Show mobile edit modal
        function showMobileEditModal() {
            if (!selectedRole) return;
            
            mobileEditTitle.textContent = `Edit ${selectedRole.name}`;
            mobileRoleName.value = selectedRole.name;
            
            // Render permissions checkboxes
            mobilePermissionsList.innerHTML = '';
            
            permissionGroups.forEach(group => {
                const groupElement = document.createElement('div');
                groupElement.className = 'permission-group';
                
                const groupPermissions = group.permissions.map(permission => {
                    const isChecked = selectedRole.permissions.includes(permission.id);
                    
                    return `
                        <div class="permission-item flex items-center p-3 hover:bg-gray-50">
                            <input type="checkbox" id="mobile-perm-${permission.id}" 
                                data-permission="${permission.id}" 
                                ${isChecked ? 'checked' : ''}
                                class="mobile-perm-checkbox h-4 w-4 text-blue-600 rounded focus:ring-blue-500">
                            <label for="mobile-perm-${permission.id}" class="ml-2 text-gray-700">${permission.name}</label>
                        </div>
                    `;
                }).join('');
                
                groupElement.innerHTML = `
                    <div class="group-header p-3 bg-gray-50">
                        <h3 class="font-medium text-gray-700">${group.name}</h3>
                    </div>
                    <div class="group-permissions grid grid-cols-2 gap-3">
                        ${groupPermissions}
                    </div>
                `;
                
                mobilePermissionsList.appendChild(groupElement);
            });
            
            mobilePermissionsModal.classList.add('hidden');
            mobileEditModal.classList.remove('hidden');
        }

        // Save mobile edit changes
        function saveMobileEditChanges() {
            if (!selectedRole) return;
            
            // Update role name
            selectedRole.name = mobileRoleName.value;
            
            // Get selected permissions
            const checkboxes = mobilePermissionsList.querySelectorAll('.mobile-perm-checkbox');
            selectedRole.permissions = [];
            
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedRole.permissions.push(parseInt(checkbox.dataset.permission));
                }
            });
            
            // Update UI
            renderPermissionsDisplay();
            closeMobileEditModal();
            showNotification('Role updated successfully!');
        }

        // Close mobile modals
        function closeMobileModal() {
            mobilePermissionsModal.classList.add('hidden');
        }

        function closeMobileEditModal() {
            mobileEditModal.classList.add('hidden');
        }

        // Render permissions display for selected role (desktop)
        function renderPermissionsDisplay() {
            if (!selectedRole) return;
            
            let html = "";
            permissionGroups.forEach(group => {
                let groupHtml = "";
                group.permissions.forEach(p => {
                    const has = selectedRole.permissions.includes(p.id);
                    if (has) {
                        groupHtml += `
                            <li class="flex items-center">
                                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                                <span class="text-gray-600">${p.name}</span>
                            </li>
                        `;
                    }
                });
                
                if (groupHtml) {
                    html += `
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-medium text-gray-700 mb-3">${group.name}</h3>
                            <ul class="space-y-2">${groupHtml}</ul>
                        </div>
                    `;
                }
            });
            
            permissionView.innerHTML = html || `
                <div class="text-center ">
                    <i class="fas fa-shield-alt text-4xl mb-3"></i>
                    <p>No permissions assigned</p>
                </div>
            `;
            
            permissionView.classList.remove('flex', 'items-center', 'justify-center', 'h-64', 'text-gray-400');
        }

        // Show role form for adding/editing (desktop)
        function showRoleForm(role = null) {
            if (role) {
                formTitle.textContent = 'Edit Role';
                formSubtitle.textContent = 'Modify role and its permissions';
                roleId.value = role.id;
                roleName.value = role.name;
                formMethod.value = 'PUT';
                roleForm.action = '/roles/' + role.id;
                submitBtn.textContent = 'Save Changes';
                selectedRole = role;
                
                // Set permissions
                document.querySelectorAll('.perm-checkbox').forEach(cb => {
                    cb.checked = selectedRole.permissions.includes(parseInt(cb.value));
                });
            } else {
                formTitle.textContent = 'Add New Role';
                formSubtitle.textContent = 'Create a new role and assign permissions';
                roleForm.reset();
                formMethod.value = 'POST';
                roleForm.action = '/roles';
                submitBtn.textContent = 'Create Role';
                selectedRole = null;
                
                // Clear all checkboxes
                document.querySelectorAll('.perm-checkbox').forEach(cb => {
                    cb.checked = false;
                });
            }
            
            roleFormContainer.classList.remove('hidden');
            permissionsDisplay.classList.add('hidden');
        }

        // Hide role form (desktop)
        function hideRoleForm() {
            roleFormContainer.classList.add('hidden');
            permissionsDisplay.classList.remove('hidden');
            
            if (selectedRole) {
                renderPermissionsDisplay();
            } else {
                permissionView.innerHTML = `
                    <div class="text-center grid grid-cols-2 gap-3">
                        <i class="fas fa-shield-alt text-4xl mb-3"></i>
                        <p>Select a role to view permissions</p>
                    </div>
                `;
                permissionView.classList.add('flex', 'items-center', 'justify-center', 'h-64', 'text-gray-400');
                editBtnContainer.classList.add('hidden');
            }
        }

        // Add a new role
        function resetForm() {
            if (window.innerWidth < 1024) {
                // On mobile, show the edit modal for new role
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

        // Edit a role
        function openEdit() {
            if (selectedRole) {
                if (window.innerWidth < 1024) {
                    showMobileEditModal();
                } else {
                    showRoleForm(selectedRole);
                }
            }
        }

        // Cancel edit
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

        // Select all permissions (desktop)
        function selectAllPermissions() {
            const checkboxes = permissionsList.querySelectorAll('.perm-checkbox');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        }

        // Select all mobile permissions
        function selectAllMobilePermissions() {
            const checkboxes = mobilePermissionsList.querySelectorAll('.mobile-perm-checkbox');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
            });
        }

        // Show notification
        function showNotification(message) {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg fade-in';
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Remove notification after 3 seconds
            setTimeout(() => {
                notification.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 500);
            }, 3000);
        }

        // Attach event listeners
        function attachEventListeners() {
            // Cancel form button (desktop)
            document.getElementById('cancelBtn').addEventListener('click', hideRoleForm);
            
            // Select all button (desktop)
            document.getElementById('selectAllBtn').addEventListener('click', selectAllPermissions);
            
            // Mobile modal buttons
            document.getElementById('closeMobileModal').addEventListener('click', closeMobileModal);
            document.getElementById('closeMobileEditModal').addEventListener('click', closeMobileEditModal);
            document.getElementById('mobileEditBtn').addEventListener('click', showMobileEditModal);
            document.getElementById('mobileCancelEditBtn').addEventListener('click', closeMobileEditModal);
            document.getElementById('mobileSaveBtn').addEventListener('click', saveMobileEditChanges);
            document.getElementById('mobileSelectAllBtn').addEventListener('click', selectAllMobilePermissions);
            
            // Close modal when clicking outside
            mobilePermissionsModal.addEventListener('click', (e) => {
                if (e.target === mobilePermissionsModal) {
                    closeMobileModal();
                }
            });
            
            mobileEditModal.addEventListener('click', (e) => {
                if (e.target === mobileEditModal) {
                    closeMobileEditModal();
                }
            });
            
            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024 && selectedRole) {
                    closeMobileModal();
                    closeMobileEditModal();
                    renderPermissionsDisplay();
                }
            });

            // Handle form submission
            roleForm.addEventListener('submit', function(e) {
                // The form will submit normally to the server
                // No need for JavaScript handling if using standard form submission
            });

            // Handle delete form submissions
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    if (!confirm('Are you sure you want to delete this role?')) {
                        e.preventDefault();
                    }
                });
            });
        }

        // Initialize the app when DOM is loaded
        document.addEventListener('DOMContentLoaded', init);
    </script>
</body>
</html>
