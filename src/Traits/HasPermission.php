<?php

namespace Rez1pro\UserAccess\Traits;

use App\Models\Permission;
use App\Models\Role;
use BackedEnum;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasPermission
{
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions', 'role_id', 'permission_id');
    }

    function givePermissionTo(BackedEnum|string|int $value)
    {
        if (is_int($value)) {
            $permission = Permission::find($value)?->id;
            throw_if(!$permission, "Permission with id {$value} not found");
        } elseif ($value instanceof BackedEnum) {
            $permission = Permission::where('name', $value->value)->first()?->id;
            throw_if(!$permission, "Permission with name {$value->value} not found");
        } else {
            $permission = Permission::where('name', $value)->first()?->id;
            throw_if(!$permission, "Permission with name {$value} not found");
        }

        $this->permissions()->syncWithoutDetaching($permission);

        return $this;
    }

    function hasPermissionTo(BackedEnum|string|int $value): bool
    {
        if (is_int($value)) {
            return $this->permissions()->where('id', $value)->exists();
        } elseif ($value instanceof BackedEnum) {
            return $this->permissions->contains('name', $value->value);
        } else {
            return $this->permissions->contains('name', $value);
        }
    }

    function removePermission(BackedEnum|string|int $value)
    {
        if (is_int($value)) {
            $permission = Permission::find($value)?->id;
            throw_if(!$permission, "Permission with id {$value} not found");
        } elseif ($value instanceof BackedEnum) {
            $permission = Permission::where('name', $value->value)->first()?->id;
            throw_if(!$permission, "Permission with name {$value->value} not found");
        } else {
            $permission = Permission::where('name', $value)->first()?->id;
            throw_if(!$permission, "Permission with name {$value} not found");
        }

        $this->permissions()->detach($permission);

        return $this;
    }

    function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }
}
