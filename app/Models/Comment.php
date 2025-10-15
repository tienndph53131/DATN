<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_id',
        'account_id',
        'content',
        'date',
        'status'
    ];
    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function account(){
        return $this->belongsTo(Account::class);
    }
}
