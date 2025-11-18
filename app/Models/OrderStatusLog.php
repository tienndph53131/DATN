<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatusLog extends Model
{
    protected $table = 'order_status_logs';

    protected $fillable = [
        'order_id',
        'old_status_id',
        'new_status_id',
        'changed_by',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function oldStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'old_status_id');
    }

    public function newStatus()
    {
        return $this->belongsTo(OrderStatus::class, 'new_status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
