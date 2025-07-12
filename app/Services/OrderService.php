<?php

namespace App\Services;

use App\DTOs\CreateOrderDTO;
use App\DTOs\ApproveOrderDTO;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function createOrder(CreateOrderDTO $orderData): array
    {
        $this->validateStockAvailability($orderData->items);

        DB::beginTransaction();

        try {
            $order = $this->createOrderRecord($orderData->userId);
            $totalPrice = $this->processOrderItems($order, $orderData->items);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Заказ успешно создан',
                'data' => [
                    'order_id' => $order->id,
                    'order_number' => $order->number,
                    'total_price' => round($totalPrice, 2),
                    'status' => $order->status
                ]
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function validateStockAvailability(array $items): void
    {
        foreach ($items as $item) {
            $product = Product::find($item->productId);

            if ($product->stock < $item->quantity) {
                throw new Exception("Недостаточно товара '{$product->name}' на складе. Доступно: {$product->stock}");
            }
        }
    }

    private function createOrderRecord(int $userId): Order
    {
        return Order::create([
            'number' => 'ORD-' . now()->timestamp,
            'status' => 'draft',
            'user_id' => $userId,
        ]);
    }

    private function processOrderItems(Order $order, array $items): float
    {
        $totalPrice = 0;

        foreach ($items as $item) {
            $product = Product::find($item->productId);

            $product->decrement('stock', $item->quantity);

            $order->items()->create([
                'product_id' => $product->id,
                'quantity' => $item->quantity,
                'price' => $product->price,
            ]);

            $totalPrice += $product->price * $item->quantity;
        }

        return $totalPrice;
    }

    public function approveOrder(ApproveOrderDTO $approveData): array
    {
        $this->validateOrderStatus($approveData->order);
        $this->validateUserBalance($approveData->user, $approveData->totalPrice);

        DB::beginTransaction();

        try {
            $this->processPayment($approveData->user, $approveData->totalPrice);
            $this->updateOrderStatus($approveData->order);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Заказ успешно подтвержден',
                'data' => [
                    'order_id' => $approveData->order->id,
                    'order_number' => $approveData->order->number,
                    'total_paid' => round($approveData->totalPrice, 2),
                    'new_balance' => round($approveData->user->balance, 2),
                    'status' => $approveData->order->status
                ]
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function validateOrderStatus(Order $order): void
    {
        if ($order->status !== 'draft') {
            throw new Exception('Заказ уже подтвержден или отменен');
        }
    }

    private function validateUserBalance(User $user, float $totalPrice): void
    {
        if ($user->balance < $totalPrice) {
            throw new Exception("Недостаточно средств на балансе. Требуется: {$totalPrice}, доступно: {$user->balance}");
        }
    }

    private function processPayment(User $user, float $totalPrice): void
    {
        $user->decrement('balance', $totalPrice);
    }

    private function updateOrderStatus(Order $order): void
    {
        $order->update(['status' => 'approved']);
    }
}
