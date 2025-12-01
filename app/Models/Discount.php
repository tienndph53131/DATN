<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Discount extends Model{
    use HasFactory;
    protected $fillable=[
        'code',
        'description',
        'discount_type',
        'discount_value',
        'start_date',
        'end_date',
        'active',
        'minimum_order_amount',
        'usage_limit'
    ];

}