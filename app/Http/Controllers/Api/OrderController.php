<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateOrderRequest;
use App\Http\Requests\ApproveOrderRequest;
use App\DTOs\CreateOrderDTO;
use App\DTOs\ApproveOrderDTO;
use App\Http\Resources\OrderResource;
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

        return (new OrderResource($result['order']))
            ->additional([
                'success' => true,
                'message' => 'Заказ успешно создан',
                'total_price' => round($result['total_price'], 2),
            ])
            ->response()
            ->setStatusCode(201);
    }

    public function approveOrder(ApproveOrderRequest $request): JsonResponse
    {
        $orderId = $request->validated()['order_id'];

        $order = Order::findOrFail($orderId);
        print_r($order);die;

        $approveData = ApproveOrderDTO::fromOrder($order);
        $result = $this->orderService->approveOrder($approveData);

        return (new OrderResource($result['order']))
            ->additional([
                'success' => true,
                'message' => 'Заказ успешно подтвержден',
                'total_paid' => round($result['total_paid'], 2),
                'new_balance' => round($result['new_balance'], 2),
            ])
            ->response()
            ->setStatusCode(200);
    }

}
