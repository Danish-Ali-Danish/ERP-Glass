<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name', 'display_name', 'guard_name'];

    // Role -> Permissions (many-to-many)
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_role')
                    ->withTimestamps();
    }

    // Role -> Users
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')
                    ->withTimestamps();
    }

    // helper: attach permission by name or id
    public function givePermission($permission)
    {
        $permId = $permission instanceof Permission ? $permission->id : Permission::where('name', $permission)->value('id');
        if ($permId) $this->permissions()->syncWithoutDetaching([$permId]);
        return $this;
    }
}
