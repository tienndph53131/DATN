<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'avatar',
        'birthday',
        'email',
        'phone',
        'sex',
        'address',
        'password',
        'role_id'
    ];
    public function role(){
        return $this->belongsTo(Role::class);
    }
    public function address(){
        return $this->hasMany(Address::class);
    }
}
