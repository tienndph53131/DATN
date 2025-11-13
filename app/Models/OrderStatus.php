<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    protected $table = 'order_status';
    protected $fillable = [
        'status_name'
    ];
    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
