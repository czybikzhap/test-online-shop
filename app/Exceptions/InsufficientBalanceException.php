<?php

namespace App\Exceptions;

use App\Models\User;


class InsufficientBalanceException extends ApiException
{
    public function __construct(User $user, float $totalPrice)
    {
        $message = "Недостаточно средств на балансе. Требуется: {$totalPrice}, доступно: {$user->balance}";

        parent::__construct($message, 409);
    }
}
