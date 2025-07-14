<?php

namespace App\Exceptions;

use App\Models\Order;

class OrderStatusException extends ApiException
{
    public function __construct(Order $order = null)
    {
        $details = [];
        if ($order) {
            $details = [
                'order_id' => $order->id,
                'order_number' => $order->number,
                'current_status' => $order->status,
            ];
        }

        parent::__construct(
            'Заказ уже подтвержден или отменен',
            409,
            'invalid_order_status',
            $details
        );
    }
}
