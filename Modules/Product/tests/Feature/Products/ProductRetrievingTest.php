<?php

namespace Modules\Product\Tests\Feature\Products;

use Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductRetrievingTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function test_check_if_retrieving_products(): void
    {
        // Arrange
        Product::factory()->count(10)->create();

        // Act
        $response = $this->getJson('/api/products');

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'images',
                        'categories',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->assertJsonCount(10, 'data');
    }

    #[Test]
    public function test_check_if_show_product_is_success(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $response = $this->getJson('/api/products/' . $product->id);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'images',
                    'categories',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
