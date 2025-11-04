<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $table = 'address';

    protected $fillable = [
        'account_id',
        'name',
        'phone',
        'email',
        'province_id',
        'province_name',
        'district_id',
        'district_name',
        'ward_id',
        'ward_name',
        'address_detail',
        'is_default',
    ];

    //Một địa chỉ thuộc về một tài khoản
    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }
}
