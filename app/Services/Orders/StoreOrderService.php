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


            $order->products()->attach(
                collect($orderData->get('products'))
                    ->mapWithKeys(
                        fn($product) => [$product['product_id'] => ['quantity' => $product['quantity']]]
                    )
            );

            $order->load('products');

            event(new OrderCreatedEvent($order));

            return $order ;
        });
    }
}
