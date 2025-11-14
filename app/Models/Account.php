<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;

class Account extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'avatar',
        'birthday',
        'email',
        'phone',
        'sex',
        'password',
        'role_id',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Một tài khoản có nhiều địa chỉ
    public function addresses()
    {
        return $this->hasMany(Address::class, 'account_id');
    }

    // Lấy địa chỉ mặc định
    public function defaultAddress()
    {
        return $this->hasOne(Address::class, 'account_id')->where('is_default', true);
    }
}

?>