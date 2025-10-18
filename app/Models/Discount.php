<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    use HasFactory;
    protected $fillable = [
       'discount_code',
       'discount_name',
       'discount_percent',
         'start_date',
         'end_date',
         'minimum_order_amount',
         'usage_limit',
         'used_count',
         'status'
    ];
  public function orders()
  {
    return $this->hasMany(Order::class);
  }
}
