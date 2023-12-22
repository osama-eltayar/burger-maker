<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use App\Jobs\CheckIngredientStock;
use App\Models\Ingredient;
use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class DecreaseProductIngredientListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCreatedEvent $event): void
    {
        $affectedIngredients = collect();

        $event->order->products->load('ingredients');

        foreach ($event->order->products as $product){
            foreach ($product->ingredients as $ingredient) {
                $ingredient->decrement('current_stock',$product->pivot->quantity * $ingredient->pivot->quantity);
                $affectedIngredients->getOrPut($ingredient->id,$ingredient);
            }
        }

        dispatch(new CheckIngredientStock($affectedIngredients));
    }
}
