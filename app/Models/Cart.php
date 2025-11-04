<?php
 namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'account_id',
        
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
     public function details()
    {
        return $this->hasMany(CartDetail::class);
    }
}
