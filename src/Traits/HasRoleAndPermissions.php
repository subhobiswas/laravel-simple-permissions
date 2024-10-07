<?php
namespace Lazycode\Permissions\Traits;

use Lazycode\Permissions\Models\Permission;
use Lazycode\Permissions\Models\Role;

trait HasRolesAndPermissions
{
    // Assign a role by name (one role per user)
    public function assignRoleByName(string $roleName)
    {
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            // Ensure the user only has one role
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    // Get the user's role
    public function getRole()
    {
        return $this->role;
    }

    // Disassociate (remove) the current role from the user
    public function removeRole()
    {
        if ($this->role) {
            $this->role()->dissociate();
            $this->save();
        }

        return $this;
    }

    // Check if the user has a specific permission
    public function hasPermission(string $permissionName)
    {
        $role = $this->role;

        if ($role) {
            return $role->permissions()->where('name', $permissionName)->exists();
        }

        return false;
    }

    // Assign a permission to the role by permission name
    public function assignPermissionToRole(string $permissionName)
    {
        $role = $this->role;
        if ($role) {
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                $role->permissions()->syncWithoutDetaching([$permission->id]);
            }
        }

        return $this;
    }

    // Remove a permission from the role by permission name
    public function removePermissionFromRole(string $permissionName)
    {
        $role = $this->role;
        if ($role) {
            $permission = Permission::where('name', $permissionName)->first();

            if ($permission) {
                $role->permissions()->detach($permission->id);
            }
        }

        return $this;
    }

    // Check if the user has the specified role
    public function hasRole(string $roleName)
    {
        return $this->role && $this->role->name === $roleName;
    }

    // Relationship for user role (one-to-one)
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
