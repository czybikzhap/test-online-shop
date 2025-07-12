<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::select('id', 'name', 'description', 'price', 'stock')
            ->where('stock', '>', 0)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
} 