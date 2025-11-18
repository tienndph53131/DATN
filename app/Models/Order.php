<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_code',
        'account_id',
        'name',
        'email',
        'phone',
        'address_id',
        'booking_date',
        'total',
        'note',
        'payment_id',
        'status_id',
        'payment_status_id'
    ];
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function address()
    {
        return $this->belongsTo(Address::class, 'address_id');
    }
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    // Quan hệ với PaymentStatus (nếu tồn tại)
    public function paymentStatus()
    {
        return $this->belongsTo(PaymentStatus::class, 'payment_status_id', 'id');
    }
    
}