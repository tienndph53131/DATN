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
        'address',
        'booking_date',
        'total',
        'note',
        'payment_id',
        'status_id' 
    ];
    public function account(){
        return $this->belongsTo(Account::class);
    }
    public function payment(){
        return $this->belongsTo(PaymentMethod::class);
    }
    public function status(){
        return $this->belongsTo(OrderStatus::class);
    }
    public function addresses()
    {
        return $this->hasMany(OrderAddress::class);
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
