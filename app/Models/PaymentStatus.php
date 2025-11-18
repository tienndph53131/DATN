<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentStatus extends Model
{
    use HasFactory;

    protected $table = 'payment_status';

    protected $fillable = ['status_name'];

    // Quan hệ với Order
    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_status_id', 'id');
    }
}
