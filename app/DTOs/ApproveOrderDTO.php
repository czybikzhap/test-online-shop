<?php

namespace App\DTOs;

use App\Models\Order;
use App\Models\User;

class ApproveOrderDTO
{
    public function __construct(
        public readonly Order $order,
        public readonly User $user,
        public readonly float $totalPrice
    ) {}

    public static function fromOrder(Order $order): self
    {
        $user = User::find($order->user_id);
        $totalPrice = $order->totalPrice();

        return new self(
            order: $order,
            user: $user,
            totalPrice: $totalPrice
        );
    }
} 