<?php

namespace App\Http\Middleware;

use App\Exceptions\OrderStatusException;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\NotEnoughStockException;
use App\Exceptions\ProductNotFoundException;
use Closure;

class BusinessExceptionMiddleware
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (
            OrderStatusException |
            InsufficientBalanceException |
            NotEnoughStockException |
            ProductNotFoundException $e
        ) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
} 