<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveOrderRequest;
use App\Http\Requests\CreateOrderRequest;
use App\Models\Order;
use App\DTOs\CreateOrderDTO;
use App\DTOs\ApproveOrderDTO;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        try {
            $orderData = CreateOrderDTO::fromArray($request->all());
            $orderService = new OrderService();

            $result = $orderService->createOrder($orderData);

            return response()->json($result, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка валидации',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании заказа: ' . $e->getMessage()
            ], 500);
        }
    }


    public function approveOrder(ApproveOrderRequest $request): JsonResponse
    {
        try {
            $orderId = $request->validated('order_id');

            $order = Order::findOrFail($orderId);

            $approveData = ApproveOrderDTO::fromOrder($order);
            $orderService = new OrderService();

            $result = $orderService->approveOrder($approveData);

            return response()->json($result);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Заказ не найден'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подтверждении заказа: ' . $e->getMessage()
            ], 500);
        }
    }

}
