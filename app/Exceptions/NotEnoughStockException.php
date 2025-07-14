<?php

namespace App\Exceptions;

class NotEnoughStockException extends ApiException
{
    public function __construct(string $productName, int $available, int $requested)
    {
        parent::__construct(
            "Недостаточно товара на складе",
            409,
            'insufficient_stock',
            [
                'product_name' => $productName,
                'requested' => $requested,
                'available' => $available,
            ]
        );
    }
}
