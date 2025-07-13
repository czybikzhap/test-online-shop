<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ApproveOrderRequest;
use App\DTOs\CreateOrderDTO;
use App\DTOs\ApproveOrderDTO;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $orderData = CreateOrderDTO::fromArray($request->validated());
        $result = $this->orderService->createOrder($orderData);

        return response()->json($result, 201);
    }

    public function approveOrder(ApproveOrderRequest $request): JsonResponse
    {
        $orderId = $request->validated('order_id');
        $order = Order::findOrFail($orderId);

        $approveData = ApproveOrderDTO::fromOrder($order);
        $result = $this->orderService->approveOrder($approveData);

        return response()->json($result);
    }

}
