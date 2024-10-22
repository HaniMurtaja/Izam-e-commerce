<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an authenticated user can place an order with valid products.
     *
     * @return void
     */
    public function test_authenticated_user_can_place_order()
    {
        
        $user = User::factory()->create();
        $product1 = Product::factory()->create(['stock' => 10]);
        $product2 = Product::factory()->create(['stock' => 5]);

        
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/orders', [
                             'products' => [
                                 ['id' => $product1->id, 'quantity' => 2],
                                 ['id' => $product2->id, 'quantity' => 3],
                             ],
                         ]);

        
        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $product1->id,
            'quantity' => 2,
        ]);
        $this->assertDatabaseHas('order_product', [
            'product_id' => $product2->id,
            'quantity' => 3,
        ]);

        
        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'stock' => 8, // 10 - 2
        ]);
        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'stock' => 2, // 5 - 3
        ]);
    }

    /**
     * Test that an authenticated user cannot place an order with invalid products.
     *
     * @return void
     */
    public function test_cannot_place_order_with_invalid_products()
    {
        
        $user = User::factory()->create();

      
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/orders', [
                             'products' => [
                                 ['id' => 999, 'quantity' => 2], // Invalid product ID
                             ],
                         ]);

       
        $response->assertStatus(422);
        $response->assertJsonValidationErrors('products.0.id');
    }

    /**
     * Test that an authenticated user can retrieve their order details.
     *
     * @return void
     */
    public function test_authenticated_user_can_retrieve_order_details()
    {
       
        $user = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product = Product::factory()->create();
        $order->products()->attach($product->id, ['quantity' => 3]);

        
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson("/api/orders/{$order->id}");

       
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'id',
                     'products' => [
                         '*' => ['id', 'name', 'pivot' => ['quantity']],
                     ],
                 ]);
    }

    /**
     * Test that an unauthenticated user cannot place an order.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_place_order()
    {
        
        $response = $this->postJson('/api/orders', [
            'products' => [
                ['id' => 1, 'quantity' => 2],
            ],
        ]);

        
        $response->assertStatus(401);
    }

    /**
     * Test that an unauthenticated user cannot retrieve order details.
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_retrieve_order_details()
    {
        
        $order = Order::factory()->create();

   
        $response = $this->getJson("/api/orders/{$order->id}");

        
        $response->assertStatus(401);
    }
}
