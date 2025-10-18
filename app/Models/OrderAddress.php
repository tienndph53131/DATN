<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderAddress extends Model
{
    use HasFactory;

    protected $table = 'order_addresses';

    protected $fillable = [
        'order_id',
        'recipient_name',
        'phone',
        'address_line',
        'ward',
        'district',
        'province',
        'ghn_location_id'
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
