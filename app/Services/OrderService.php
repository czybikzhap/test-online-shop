<?php

namespace App\Services;

use App\DTOs\CreateOrderDTO;
use App\Models\Order;
use App\Models\Product;
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
                    'total_price' => $totalPrice,
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
} 