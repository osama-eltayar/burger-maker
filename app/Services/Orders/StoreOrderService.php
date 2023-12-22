<?php

namespace App\Services\Orders;

use App\Events\OrderCreatedEvent;
use App\Models\Order;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StoreOrderService
{
    public function execute(Collection $orderData): Order
    {
        return DB::transaction(function () use ($orderData) {
            $order = Order::create([]);

            $order = $this->attachProductsToOrder($order,$orderData);

            event(new OrderCreatedEvent($order));

            return $order ;
        });
    }

    private function attachProductsToOrder(Order $order, Collection $orderData)
    {
        $order->products()->attach(
            collect($orderData->get('products'))
                ->mapWithKeys(
                    fn($product) => [$product['product_id'] => ['quantity' => $product['quantity']]]
                )
        );

        return $order->load('products');
    }

}
