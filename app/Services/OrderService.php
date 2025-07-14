<?php

namespace App\Services;

use App\DTOs\CreateOrderDTO;
use App\DTOs\ApproveOrderDTO;
use App\Exceptions\InsufficientBalanceException;
use App\Exceptions\NotEnoughStockException;
use App\Exceptions\OrderStatusException;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function createOrder(CreateOrderDTO $orderData): array
    {
        $this->validateStockAvailability($orderData->items);

        DB::beginTransaction();

        try {
            $order = Order::createDraft($orderData->userId);
            $totalPrice = $this->processOrderItems($order, $orderData->items);

            DB::commit();

            return [
                'order' => $order,
                'total_price' => $totalPrice,
            ];

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    private function validateStockAvailability(array $items): void
    {
        $this->getValidatedProductsByItems($items);
    }


    private function processOrderItems(Order $order, array $items): float
    {
        return DB::transaction(function () use ($order, $items) {
            $products = $this->getValidatedProductsByItems($items);

            $totalPrice = 0;
            $itemsToCreate = [];
            $stockUpdates = [];

            foreach ($items as $item) {
                $product = $products->get($item->productId);

                $totalPrice += $product->price * $item->quantity;

                $itemsToCreate[] = [
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $item->quantity,
                    'price' => $product->price,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $stockUpdates[] = [
                    'id' => $product->id,
                    'stock' => $product->stock - $item->quantity,
                ];
            }

            if (!empty($itemsToCreate)) {
                $order->items()->insert($itemsToCreate);
            }

            if (!empty($stockUpdates)) {
                foreach ($stockUpdates as $update) {
                    Product::where('id', $update['id'])
                        ->update(['stock' => $update['stock']]);
                }
            }

            return $totalPrice;
        });
    }

    public function approveOrder(ApproveOrderDTO $approveData): array
    {
        DB::beginTransaction();

        try {
            $this->validateOrderStatus($approveData->order);
            $this->validateUserBalance($approveData->user, $approveData->totalPrice);

            $this->processPayment($approveData->user, $approveData->totalPrice);
            $this->updateOrderStatus($approveData->order);

            DB::commit();

            return [
                'order' => $approveData->order,
                'total_paid' => $approveData->totalPrice,
                'new_balance' => $approveData->user->balance,
            ];

        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    private function validateOrderStatus(Order $order): void
    {
        if ($order->status !== 'draft') {
            throw new OrderStatusException($order);
        }
    }

    private function validateUserBalance(User $user, float $totalPrice): void
    {
      if ($user->balance < $totalPrice) {
              throw new InsufficientBalanceException($user, $totalPrice);
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


    private function getValidatedProductsByItems(array $items): Collection
    {
        $productIds = collect($items)->pluck('productId')->unique()->toArray();

        $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item->productId);

            if ($product->stock < $item->quantity) {
                throw new NotEnoughStockException(
                    $product->name,
                    $product->stock,
                    $item->quantity
                );
            }
        }

        return $products;
    }
}
