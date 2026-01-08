<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class M_Counter extends Model
{
  use HasFactory;

  protected $table = 'counters';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'name',
    'group_id',
    'status',
    'color_id'
  ];

  // public function hasOneRole() : HasOne
  // {
  //     return $this -> hasOne(M_Role::class, 'loket_id');
  // }

  public function hasManyUsers(): HasMany
  {
    return $this->hasMany(M_User::class, 'counter_id');
  }

  public function hasManyTickets(): HasMany
  {
    return $this->hasMany(M_Ticket::class, 'counter_id');
  }

  public function Color(): BelongsTo
  {
    return $this->belongsTo(M_Color::class, 'color_id', 'id');
  }

  public function Categories(): BelongsToMany
  {
    return $this->belongsToMany(M_Counter_Category::class, 'relation_counter_categories', 'counter_id', 'counter_category_id');
  }

  public function Group(): BelongsTo
  {
    return $this->belongsTo(M_Group::class, 'group_id', 'id');
  }
}
