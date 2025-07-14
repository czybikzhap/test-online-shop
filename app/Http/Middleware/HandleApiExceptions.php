<?php

namespace App\Http\Middleware;

use Closure;
use Throwable;
use Illuminate\Http\Request;

class HandleApiExceptions
{
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Внутренняя ошибка сервера',
                    'error' => config('app.debug') ? $e->getMessage() : null,
                ], 500);
            }

            throw $e;
        }
    }
}
