<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class M_Queue extends Model
{
  use HasFactory;

  protected $table = 'queues';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'code',
    'group_id'
  ];

  public function Group(): BelongsTo
  {
    return $this->belongsTo(M_Group::class, 'group_id', 'id');
  }
}
