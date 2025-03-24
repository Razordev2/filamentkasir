<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Discount extends Model
{
use HasFactory;

protected $fillable = ['type', 'value', 'expires_at', 'code', 'quota'];

protected $casts = [
    'expires_at' => 'datetime',
];

protected static function boot()
{
    parent::boot();

    static::creating(function ($discount) {
        if (empty($discount->code)) {
            $prefix = match ($discount->type) {
                'percentage' => 'PERC',
                'fixed' => 'FIXED',
                'buy1get1' => 'B1G1',
                'voucher' => 'VCHR',
                default => 'DISC',
            };
            $discount->code = strtoupper($prefix . '-' . Str::random(6));
        }
    });
}

public function isValid(): bool
{
    return $this->quota > 0 && (!$this->expires_at || $this->expires_at->isFuture());
}

public function getDiscountedPrice($price)
{
    if ($this->type === 'percentage') {
        return max(0, $price - ($price * $this->value / 100));
    } elseif ($this->type === 'fixed') {
        return max(0, $price - $this->value);
    } elseif ($this->type === 'buy1get1') {
        return $price / 2;
    } elseif ($this->type === 'voucher') {
        return max(0, $price - $this->value);
    }
    return $price;
}

public function useDiscount()
{
    if (!$this->isValid()) {
        throw new \Exception('Kode diskon tidak valid atau sudah habis.');
    }
    if ($this->quota > 0) {
        $this->decrement('quota');
    }
}

protected static function handleDiscountUsage(array $data, ?Product $record = null): array
{
    if ($record) {
        $oldDiscountCode = $record->discount_code;
        $newDiscountCode = $data['discount_code'] ?? null;
        if ($oldDiscountCode === $newDiscountCode) {
            return $data;
        }
        if ($oldDiscountCode) {
            $oldDiscount = Discount::where('code', $oldDiscountCode)->first();
            if ($oldDiscount) {
                $oldDiscount->increment('quota', 1);
            }
        }
    }
    if (!empty($data['discount_code'])) {
        $discount = Discount::where('code', $data['discount_code'])->first();

        if (!$discount || !$discount->isValid()) { 
            throw new \Exception('Kode diskon tidak valid atau sudah habis.');
        }
        $discount->useDiscount();
    }
    
    return $data;
}
}
