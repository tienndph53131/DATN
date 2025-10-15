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
       'minimum_order_amout',
       'usage_limit',
       'status'
    ];
}
