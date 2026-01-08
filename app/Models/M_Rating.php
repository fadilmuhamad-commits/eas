<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class M_Rating extends Model
{
    use HasFactory;

    protected $table = 'user_ratings';

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'value',
        'user_id',
        'ticket_id'
    ];

    public function User(): BelongsTo
    {
        return $this->belongsTo(M_User::class, 'user_id', 'id');
    }

    public function Ticket(): BelongsTo
    {
        return $this->belongsTo(M_Ticket::class, 'ticket_id', 'id');
    }
}
