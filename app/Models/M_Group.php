<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class M_Group extends Model
{
  use HasFactory;

  protected $table = 'groups';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'name'
  ];

  public function hasManyCounters(): HasMany
  {
    return $this->hasMany(M_Counter::class, 'group_id');
  }

  public function hasManyQueues(): HasMany
  {
    return $this->HasMany(M_Queue::class, 'group_id');
  }
}
