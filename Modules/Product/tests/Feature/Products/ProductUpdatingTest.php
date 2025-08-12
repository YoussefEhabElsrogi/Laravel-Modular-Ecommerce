<?php

namespace Modules\Product\Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Modules\Category\Models\Category;
use Modules\Product\Models\Product;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductUpdatingTest extends TestCase
{
    use RefreshDatabase;

    const TABLE_NAME = 'products';

    public function setUp(): void
    {
        parent::setUp();

        $uploadPath = public_path('uploads/products');
        if (is_dir($uploadPath)) {
            $files = glob($uploadPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }
    }

    #[Test]
    public function test_update_product(): void
    {
        $categories = Category::factory()->count(2)->create();

        $product = Product::factory()->create();

        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'High-performance laptop for gaming',
                'ar' => 'حاسوب محمول عالي الأداء للألعاب',
            ],
            'price' => 1200,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
                UploadedFile::fake()->image('image2.png'),
            ],
            'categories' => $categories->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->postJson('/api/products/' . $product->id, $data);

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'message' => __('messages.products.update_success'),
            ]);

        $responseData = $response->json('data');

        $this->assertDatabaseHas(self::TABLE_NAME, [
            'name->en' => 'Laptop',
            'name->ar' => 'حاسوب محمول',
            'price' => 1200,
        ]);

        foreach ($responseData['images'] as $url) {
            $this->assertFileExists(storage_path('app/public/uploads/products/' . basename($url)));
        }
    }
}
