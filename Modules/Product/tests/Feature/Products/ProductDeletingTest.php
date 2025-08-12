<?php

namespace Modules\Product\Tests\Feature\Products;

use Modules\Product\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductDeletingTest extends TestCase
{
    use RefreshDatabase;

    const TABLE_NAME = 'products';


    #[Test]
    public function test_delete_product(): void
    {
        // Arrange
        $product = Product::factory()->create();

        // Act
        $response = $this->deleteJson('/api/products/' . $product->id);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.products.delete_success'),
                'data' => []
            ]);

        $this->assertDatabaseMissing(self::TABLE_NAME, $product->toArray());
    }
}
