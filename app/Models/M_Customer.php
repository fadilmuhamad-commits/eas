<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class M_Customer extends Model
{
  use HasFactory;

  protected $table = 'customers';

  protected $primaryKey = 'id';

  protected $fillable = [
    'id',
    'registration_code',
    'name',
    'email',
    'phone_number',
    'address',
    'birth_place',
    'birth_date',
    'type'
  ];

  public function hasManyTickets(): HasMany
  {
    return $this->hasMany(M_Ticket::class, 'customer_id');
  }
}
