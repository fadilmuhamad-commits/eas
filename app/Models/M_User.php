<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class M_User extends Authenticatable
{
  use HasFactory;

  protected $table = 'users';

  protected $primaryKey = 'id';

  protected $fillable = [
    'name',
    'username',
    'email',
    'password',
    'rating',
    'role_id',
    'counter_id'
  ];

  public function Role(): BelongsTo
  {
    return $this->belongsTo(M_Role::class, 'role_id', 'id');
  }

  public function Counter(): BelongsTo
  {
    return $this->belongsTo(M_Counter::class, 'counter_id', 'id');
  }
}
