<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class M_Role extends Model
{
  use HasFactory;

  protected $table = 'roles';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'name',
  ];

  public function hasManyUsers(): HasMany
  {
    return $this->hasMany(M_User::class, 'role_id');
  }

  public function Permissions(): BelongsToMany
  {
    return $this->belongsToMany(M_Permission::class, 'relation_role_permissions', 'role_id', 'permission_id');
  }

  public function hasPermission(string $permission)
  {
    return $this->Permissions()->where('name', $permission)->first();
  }

  public function hasAnyPermission(array $permissions)
  {
    $rolePermissions = $this->Permissions()->pluck('name')->toArray();

    foreach ($permissions as $permission) {
      if (in_array($permission, $rolePermissions)) {
        return true;
      }
    }

    return false;
  }

  // public function Loket(): BelongsTo
  // {
  //     return $this->belongsTo(M_Loket::class, 'loket_id', 'id');
  // }
}
