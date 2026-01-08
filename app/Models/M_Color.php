<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class M_Color extends Model
{
    use HasFactory;

    protected $table = 'colors';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'hexcode'
    ];

    public function hasOneCounter(): HasOne
    {
        return $this->hasOne(M_Counter::class, 'color_id');
    }

    public function hasOneConfig1(): HasOne
    {
        return $this->hasOne(M_Config::class, 'color1_id');
    }

    public function hasOneConfig2(): HasOne
    {
        return $this->hasOne(M_Config::class, 'color2_id');
    }

    public function hasOneConfig3(): HasOne
    {
        return $this->hasOne(M_Config::class, 'color3_id');
    }
}
