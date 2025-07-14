<?php

namespace App\Exceptions;

use App\Models\Order;

class OrderStatusException extends ApiException
{
    public function __construct(Order $order = null)
    {
        $message = 'Заказ уже подтвержден или отменен';

        if ($order) {
            $message .= " (Order #{$order->number})";
        }

        parent::__construct($message, 409);
    }

}
