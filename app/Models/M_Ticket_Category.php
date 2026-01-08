<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class M_Ticket_Category extends Model
{
  use HasFactory;

  protected $table = 'ticket_categories';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'name',
    'code'
  ];

  public function hasManyTickets(): HasMany
  {
    return $this->HasMany(M_Ticket::class, 'ticket_category_id');
  }
}
