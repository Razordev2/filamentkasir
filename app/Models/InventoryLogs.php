<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLogs extends Model
{
protected $fillable = [
        'product_id',
        'tanggal',
        'stock_change',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
}
