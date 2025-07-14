<?php

namespace App\Exceptions;

use App\Models\User;

class InsufficientBalanceException extends ApiException
{
    public function __construct(User $user, float $totalPrice)
    {
        parent::__construct(
            "Недостаточно средств на балансе",
            409,
            'insufficient_balance',
            [
                'required' => $totalPrice,
                'available' => $user->balance,
                'user_id' => $user->id,
            ]
        );
    }
}
