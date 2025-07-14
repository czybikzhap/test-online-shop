<?php

namespace App\Exceptions;

class ProductNotFoundException extends ApiException
{
    public function __construct(int $productId)
    {
        parent::__construct("Товар с ID {$productId} не найден", 404);
    }
}
