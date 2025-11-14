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
        'booking_date',
        'total',
        'note',
        'payment_id',
        'status_id',
        'discount_id'
    ];

    /**
     * Get the details for the order.
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    /**
     * Get the account that owns the order.
     */
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    /**
     * Get the shipping address for the order.
     */
    public function orderAddress()
    {
        return $this->hasOne(OrderAddress::class);
    }

    /**
     * Get the payment method for the order.
     */
    public function payment()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_id');
    }

    /**
     * Get the status of the order.
     */
    public function status()
    {
        return $this->belongsTo(OrderStatus::class, 'status_id');
    }

    /**
     * Get the discount associated with the order.
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
