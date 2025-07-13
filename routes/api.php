<?php

use App\Http\Controllers\Api\OrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CatalogController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| API эндпоинты для онлайн-магазина
|
*/

Route::get('/catalog', [CatalogController::class, 'index']);
Route::post('/create-order', [OrderController::class, 'createOrder']);
Route::post('/approve-order', [OrderController::class, 'approveOrder']);

