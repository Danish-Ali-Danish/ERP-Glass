<div class="col-12 col-lg-4">
    <div class="roles-container p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h4 text-secondary mb-0">Roles</h2>
            <button type="button" onclick="resetForm()" class="btn btn-primary-custom d-flex align-items-center">
                <i class="fas fa-plus me-2"></i> Add New Role
            </button>
        </div>
        
        <div class="d-flex flex-column gap-3" id="rolesList">
            @foreach($roles as $role)
                <div class="role-card border rounded p-3" 
                     id="roleBtn{{ $role->id }}" onclick="selectRole({{ $role->id }})">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h3 class="h5 fw-semibold text-dark mb-1">{{ $role->name }}</h3>
                            <p class="small text-muted mb-0">{{ $role->permissions->count() }} permissions</p>
                        </div>
                        <div class="d-flex gap-2">
                            <form method="POST" action="{{ route('roles.destroy', $role) }}" class="delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger p-1" onclick="return confirm('Are you sure you want to delete this role?')">
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
