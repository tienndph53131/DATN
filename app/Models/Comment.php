<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{    
    use HasFactory;
    public $timestamps = false; // migration doesn't include created_at/updated_at

    protected $fillable = [
        'product_id',
        'account_id',
        'content',
        'rating',
        'date',
        'status',
    ];



    protected $dates = [
        'date',
    ];
    
    protected $casts = [
        'date' => 'datetime',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
