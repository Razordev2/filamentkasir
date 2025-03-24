<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'password', 'phone', 'address', 'points'];

    protected $hidden = ['password'];

    public function productRedeems()
    {
        return $this->hasMany(ProductRedeem::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
