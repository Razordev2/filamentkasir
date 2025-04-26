<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

protected $fillable = ['name', 'category_id', 'price', 'stock', 'discount_code', 'images', 'points_required'];

    protected $appends = ['discounted_price', 'image_url'];
    
    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLogs::class, 'product_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_code', 'code');
    }

    public function getDiscountedPriceAttribute()
    {
        return $this->discount ? $this->discount->getDiscountedPrice($this->price) : $this->price;
    }    

    public function getImageUrlAttribute()
    {
        return asset('storage/' . $this->images);
    }
    
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 2, ',', '.');
    }
    public function getFormattedDiscountedPriceAttribute()
    {
        return 'Rp ' . number_format($this->discounted_price, 2, ',', '.');
    }
    
}