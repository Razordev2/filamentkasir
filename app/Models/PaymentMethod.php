<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = ['paymentmethods'];

    public function orders()
    {
        return $this->hasMany(Order::class, 'payment_method_id');
    }
}
