<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class M_Permission extends Model
{
  use HasFactory;

  protected $table = 'permissions';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'name',
    'description'
  ];

  public function Roles(): BelongsToMany
  {
    return $this->belongsToMany(M_Role::class, 'relation_role_permissions', 'permission_id', 'role_id');
  }
}
