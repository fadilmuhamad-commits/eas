<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class M_Config extends Model
{
  use HasFactory;

  protected $table = 'config';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'logo1',
    'logo2',
    'loading',
    'instance_name',
    'running_text',
    'color1_id',
    'color2_id',
    'color3_id',
    'status',
    'partnership',
    'partner_api'
  ];

  public function Color1(): BelongsTo
  {
    return $this->belongsTo(M_Color::class, 'color1_id', 'id');
  }

  public function Color2(): BelongsTo
  {
    return $this->belongsTo(M_Color::class, 'color2_id', 'id');
  }

  public function Color3(): BelongsTo
  {
    return $this->belongsTo(M_Color::class, 'color3_id', 'id');
  }
}
