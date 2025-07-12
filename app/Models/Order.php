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
