<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Order extends Model
{   
    use HasFactory;

    protected $fillable = ['customer_name', 'customer_email', 'customer_phone', 'total_price', 'payment_method_id','order_id', 'product_id', 'quantity', 'unit_price', 'discounted_price','notes'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
    protected static function boot()
{
    parent::boot();

    static::creating(function ($order) {
        $product = Product::find($order->product_id);
        if ($order->quantity > $product->stock) {
            throw new \Exception("Stok tidak mencukupi. Hanya tersedia {$product->stock} unit.");
        }
    });

    static::created(function ($order) {
        $product = Product::find($order->product_id);
        $product->stock -= $order->quantity;
        $product->save();
    });

    static::deleting(function ($order) {
        $product = Product::find($order->product_id);
        $product->stock += $order->quantity;
        $product->save();
    });
}
}
