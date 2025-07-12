<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\DTOs\CreateOrderDTO;
use App\DTOs\ApproveOrderDTO;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function createOrder(Request $request): JsonResponse
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

    public function approveOrder(Request $request, Order $order): JsonResponse
    {
        try {
            // Отладочная информация
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Заказ не найден'
                ], 404);
            }

            $approveData = ApproveOrderDTO::fromOrder($order);
            $orderService = new OrderService();

            $result = $orderService->approveOrder($approveData);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подтверждении заказа: ' . $e->getMessage()
            ], 500);
        }
    }

    public function approveOrderByBody(Request $request): JsonResponse
    {
        try {
            $orderId = $request->input('order_id');
            
            if (!$orderId) {
                return response()->json([
                    'success' => false,
                    'message' => 'ID заказа не указан'
                ], 400);
            }

            $order = Order::find($orderId);
            
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Заказ не найден'
                ], 404);
            }

            $approveData = ApproveOrderDTO::fromOrder($order);
            $orderService = new OrderService();

            $result = $orderService->approveOrder($approveData);

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подтверждении заказа: ' . $e->getMessage()
            ], 500);
        }
    }

}
