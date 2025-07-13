<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Illuminate\Database\Eloquent\Builder available()
 */

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'stock'
    ];

    public function scopeAvailable($query)
    {
        return $query->select('id', 'name', 'description', 'price', 'stock')
            ->where('stock', '>', 0);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderProduct::class);
    }
}
