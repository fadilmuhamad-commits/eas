<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class M_Ticket extends Model
{
  use HasFactory;

  protected $table = 'tickets';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'booking_code',
    'queue_number',
    'customer_id',
    'counter_category_id',
    'ticket_category_id',
    'position',
    'counter_id',
    'status',
    'duration',
    'note',
    'counter_category_code',
    'ticket_category_name',
    'counter_name',
    'group_name',
  ];

  public function Customer(): BelongsTo
  {
    return $this->belongsTo(M_Customer::class, 'customer_id', 'id');
  }

  public function Counter(): BelongsTo
  {
    return $this->belongsTo(M_Counter::class, 'counter_id', 'id');
  }

  public function Counter_Category(): BelongsTo
  {
    return $this->belongsTo(M_Counter_Category::class, 'counter_category_id', 'id');
  }

  public function Ticket_Category(): BelongsTo
  {
    return $this->belongsTo(M_Ticket_Category::class, 'ticket_category_id', 'id');
  }
}
