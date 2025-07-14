<?php

namespace App\Exceptions;

class NotEnoughStockException extends ApiException
{
    public function __construct(string $productName, int $available, int $requested)
    {
        $message = "Недостаточно товара '{$productName}' на складе. Запрошено: {$requested}, доступно: {$available}";

        parent::__construct($message, 409);
    }
}
