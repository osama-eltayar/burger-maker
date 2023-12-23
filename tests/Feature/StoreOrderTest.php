<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Merchant;
use App\Models\Product;
use App\Notifications\BuyBackIngredient;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class StoreOrderTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_cant_store_order_without_products(): void
    {
        $response = $this->postJson('api/orders');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('products');
    }

    public function test_cant_store_order_without_quantity(): void
    {
        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => 5,
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('products.0.quantity');
    }

    public function test_cant_store_order_with_invalid_product_id(): void
    {
        Product::factory()->create();
        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => 5,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('products.0.product_id');
    }

    public function test_cant_store_order_has_product_without_enough_ingredients(): void
    {
        Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'needed_stock' => 100,
            'current_stock' => 20
            ]);
        $product->ingredients()->attach($ingredient->id , ['quantity' => 25]);

        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 1
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['some ingredients is not available']);
    }

    public function test_cant_store_order_has_quantity_more_than_stock(): void
    {
        Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'needed_stock' => 100,
            'current_stock' => 50
        ]);
        $product->ingredients()->attach($ingredient->id , ['quantity' => 25]);

        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 3
                ]
            ]
        ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['some ingredients is not available']);
    }

    public function test_can_store_order(): void
    {
        Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'needed_stock' => 100,
            'current_stock' => 50
        ]);
        $product->ingredients()->attach($ingredient->id , ['quantity' => 25]);

        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonIsObject('data.order');
        $response->assertJsonFragment(['order created Successfully']);
    }

    public function test_can_store_order_and_decrease_ingredient_stock(): void
    {
        Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'needed_stock' => 100,
            'current_stock' => 70
        ]);
        $product->ingredients()->attach($ingredient->id , ['quantity' => 25]);

        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonIsObject('data.order');
        $response->assertJsonFragment(['order created Successfully']);
        self::assertEquals(20,$ingredient->refresh()->current_stock);
    }

    public function test_can_store_order_and_decrease_ingredient_stock_and_notify_merchant(): void
    {
        Notification::fake();

        $merchant = Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'needed_stock' => 100,
            'current_stock' => 70
        ]);
        $product->ingredients()->attach($ingredient->id , ['quantity' => 25]);

        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonIsObject('data.order');
        $response->assertJsonFragment(['order created Successfully']);
        self::assertEquals(20,$ingredient->refresh()->current_stock);
        Notification::assertSentTo(
            [$merchant], BuyBackIngredient::class
        );
    }

    public function test_can_store_order_and_decrease_ingredient_stock_and_notify_merchant_only_once(): void
    {
        Notification::fake();

        $merchant = Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'needed_stock' => 500,
            'current_stock' => 250
        ]);
        $product->ingredients()->attach($ingredient->id , ['quantity' => 25]);

        $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);



        $response->assertStatus(200);
        $response->assertJsonIsObject('data.order');
        $response->assertJsonFragment(['order created Successfully']);
        self::assertEquals(150,$ingredient->refresh()->current_stock);
        Notification::assertSentTo(
            [$merchant], BuyBackIngredient::class
        );

        Notification::assertCount(1);
    }

    public function test_can_store_order_and_decrease_ingredient_stock_without_notify_merchant(): void
    {
        Notification::fake();

        $merchant = Merchant::factory()->create();
        $product = Product::factory()->create();
        $ingredient = Ingredient::factory()->create([
            'needed_stock' => 500,
            'current_stock' => 400
        ]);
        $product->ingredients()->attach($ingredient->id , ['quantity' => 25]);

        $response = $this->postJson('api/orders',[
            'products' => [
                [
                    'product_id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonIsObject('data.order');
        $response->assertJsonFragment(['order created Successfully']);
        self::assertEquals(350,$ingredient->refresh()->current_stock);
        Notification::assertNotSentTo(
            [$merchant], BuyBackIngredient::class
        );
    }


}
