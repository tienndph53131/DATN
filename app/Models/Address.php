<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;
    protected $fillable = [
        'account_id',
        'name',
        'phone',
        'email',
        'city',
        'district',
        'ward',
        'address_detail',
        'is_default'
    ];
    public function account(){
        return $this->belongsTo(Account::class);
    }
}
