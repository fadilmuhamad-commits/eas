<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class M_Counter_Category extends Model
{
    use HasFactory;

    protected $table = 'counter_categories';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'code',
        'color_id'
    ];

    public function hasManyTickets(): HasMany
    {
        return $this->HasMany(M_Ticket::class, 'counter_category_id');
    }

    public function Color(): BelongsTo
    {
        return $this->belongsTo(M_Color::class, 'color_id', 'id');
    }

    public function Counters(): BelongsToMany
    {
        return $this->belongsToMany(M_Counter::class, 'relation_counter_categories', 'counter_category_id', 'counter_id');
    }
}
