<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    public function index(): JsonResponse
    {
        $products = Product::available()->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}
