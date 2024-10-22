<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_product()
    {
        
        $data = [
            'name' => 'Test Product',
            'price' => 50.00,
            'stock' => 100,
        ];

      
        $product = Product::create($data);

        
        $this->assertInstanceOf(Product::class, $product); 
        $this->assertDatabaseHas('products', $data); 
    }

    /** @test */
    public function it_requires_a_name_to_create_a_product()
    {
        
        $data = [
            'price' => 50.00,
            'stock' => 100,
        ];

        
        $this->expectException(\Illuminate\Database\QueryException::class);
        Product::create($data);

       
        $this->assertDatabaseMissing('products', ['price' => 50.00]);
    }
}
