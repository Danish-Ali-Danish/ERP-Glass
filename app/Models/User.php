<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'address',
        'image',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // Single role relation
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    /**
     * Get user permissions through role
     */
    public function permissions()
    {
        return $this->role?->permissions ?? collect();
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_string($role)) {
            return $this->role->name === $role;
        }

        return $this->role->id === $role->id;
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole($roles): bool
    {
        if (!$this->role) {
            return false;
        }

        if (is_array($roles)) {
            foreach ($roles as $role) {
                if ($this->hasRole($role)) {
                    return true;
                }
            }
            return false;
        }

        return $this->hasRole($roles);
    }

    /**
     * Assign role to user
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        $this->role_id = $role->id;
        $this->save();

        return $this;
    }

    /**
     * Remove role from user
     */
    public function removeRole($role)
    {
        if ($this->role && (
            (is_string($role) && $this->role->name === $role) ||
            ($this->role->id === $role->id)
        )) {
            $this->role_id = null;
            $this->save();
        }

        return $this;
    }

    /**
     * Replace user role
     */
    public function syncRoles($roles)
    {
        if (is_array($roles)) {
            $role = is_string($roles[0]) 
                ? Role::where('name', $roles[0])->firstOrFail()
                : $roles[0];
        } else {
            $role = is_string($roles)
                ? Role::where('name', $roles)->firstOrFail()
                : $roles;
        }

        $this->role_id = $role->id;
        $this->save();

        return $this;
    }

    /**
     * Check if user has a specific permission by name
     */
    public function hasPermission(string $permissionName): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->permissions()->pluck('name')->contains($permissionName);
    }

    /**
     * Check if user has ALL given permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if user has permission for a given route (via permission_router table)
     */
    public function hasPermissionForRoute(string $routeName): bool
    {
        $permissionIds = $this->permissions()->pluck('id');

        return DB::table('permission_router')
            ->whereIn('permission_id', $permissionIds)
            ->where('route_name', $routeName)
            ->exists();
    }

    protected static function booted()
    {
        static::saving(function ($user) {
            if ($user->role_id) {
                $role = Role::find($user->role_id);
                $user->role = $role ? $role->name : null;
            }
        });
    }

    public function roleRelation()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
