<?php

namespace Lazycode\Permissions\Traits;

use Lazycode\Permissions\Models\Permission;
use Lazycode\Permissions\Models\Role;

trait HasRolesAndPermissions
{
    /**
     * Assign a role to the user by role name.
     * Each user can have only one role.
     *
     * @param string $roleName
     * @return $this
     */
    public function assignRole(string $roleName): self
    {
        $role = Role::where('name', $roleName)->first();

        if ($role) {
            // Ensure the user only has one role
            $this->role()->associate($role);
            $this->save();
        }

        return $this;
    }

    /**
     * Get the current role of the user.
     *
     * @return Role|null
     */
    public function getRole(): ?Role
    {
        return $this->role;
    }

    /**
     * Disassociate (remove) the current role from the user.
     *
     * @return $this
     */
    public function removeRole(): self
    {
        if ($this->role) {
            $this->role()->dissociate();
            $this->save();
        }

        return $this;
    }

    /**
     * Assign a permission to the user's role by permission name.
     *
     * @param string $permissionName
     * @return $this
     */
    public function assignPermissionToRole(string $permissionName): self
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

    /**
     * Remove a permission from the user's role by permission name.
     *
     * @param string $permissionName
     * @return $this
     */
    public function removePermissionFromRole(string $permissionName): self
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

    /**
     * Check if the user has the specified role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->role && $this->role->name === $roleName;
    }

    /**
     * Define a one-to-one relationship with the Role model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Check if the user has any of the specified roles.
     *
     * @param string ...$roles
     * @return bool
     */
    public function hasAnyRole(string ...$roles): bool
    {
        return $this->roles->whereIn('name', $roles)->isNotEmpty();
    }

    /**
     * Check if the user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('name', $permission);
    }

    /**
     * Check if the user has any of the specified permissions.
     *
     * @param string ...$permissions
     * @return bool
     */
    public function hasAnyPermission(string ...$permissions): bool
    {
        return $this->permissions->whereIn('name', $permissions)->isNotEmpty();
    }

    /**
     * Check if the user has all of the specified permissions.
     *
     * @param string ...$permissions
     * @return bool
     */
    public function hasAllPermissions(string ...$permissions): bool
    {
        return $this->permissions->pluck('name')->intersect($permissions)->count() == count($permissions);
    }

    /**
     * Check if the user has exactly the specified permissions.
     *
     * @param string ...$permissions
     * @return bool
     */
    public function hasExactPermissions(string ...$permissions): bool
    {
        return $this->permissions->pluck('name')->sort()->values()->all() === collect($permissions)->sort()->values()->all();
    }
}
