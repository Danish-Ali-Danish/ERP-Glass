
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