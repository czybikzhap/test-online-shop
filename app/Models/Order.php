<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'number',
        'status',
        'user_id'
    ];


    public static function createDraft(int $userId): self
    {
        return static::create([
            'number' => static::generateOrderNumber(),
            'status' => 'draft',
            'user_id' => $userId,
        ]);
    }

    protected static function generateOrderNumber(): string
    {
        return 'ORD-' . now()->timestamp;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function totalPrice(): float
    {
        return round($this->items->sum(fn($item) => $item->price * $item->quantity), 2);
    }
}
