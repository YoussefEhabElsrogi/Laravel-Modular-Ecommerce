<?php

namespace Modules\Product\Tests\Feature\Products;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Modules\Category\Models\Category;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProductCreatingTest extends TestCase
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
    public function test_create_product(): void
    {
        $categories = Category::factory()->count(2)->create();

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
        $response = $this->postJson('/api/products', $data);

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.products.create_success'),
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

    #[Test]
    public function test_product_name_is_required(): void
    {
        $categories = Category::factory()->count(1)->create();

        $data = [
            'name' => [
                'en' => '',
                'ar' => '',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => 1200,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
            ],
            'categories' => $categories->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->postJson('/api/products', $data);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name.en',
                    'name.ar',
                ],
            ]);
    }

    #[Test]
    public function test_product_description_is_required(): void
    {
        // Arrange
        $categories = Category::factory()->count(1)->create();

        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => '',
                'ar' => '',
            ],
            'price' => 1200,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
            ],
            'categories' => $categories->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->postJson('/api/products', $data);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'description.en',
                    'description.ar',
                ],
            ]);
    }

    #[Test]
    public function test_product_price_is_required(): void
    {
        // Arrange
        $categories = Category::factory()->count(1)->create();

        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => '',
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
            ],
            'categories' => $categories->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->postJson('/api/products', $data);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'price',
                ],
            ]);
    }

    #[Test]
    public function test_product_price_must_be_numeric(): void
    {
        // Arrange
        $categories = Category::factory()->count(1)->create();

        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => 'not-a-number',
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
            ],
            'categories' => $categories->pluck('id')->toArray(),
        ];

        // Act
        $response = $this->postJson('/api/products', $data);

        // Assert
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'price',
                ],
            ]);
    }

    #[Test]
    public function test_product_images_are_required(): void
    {
        $categories = Category::factory()->count(1)->create();

        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => 1200,
            'images' => [],
            'categories' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/products', $data);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'images',
                ],
            ]);
    }

    #[Test]
    public function test_product_images_must_be_valid_image(): void
    {
        $categories = Category::factory()->count(1)->create();

        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => 1200,
            'images' => [
                UploadedFile::fake()->create('invalid.txt'),
            ],
            'categories' => $categories->pluck('id')->toArray(),
        ];

        $response = $this->postJson('/api/products', $data);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'images.0',
                ],
            ]);
    }

    #[Test]
    public function test_product_categories_are_required(): void
    {
        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => 1200,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
            ],
            'categories' => [],
        ];

        $response = $this->postJson('/api/products', $data);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'categories',
                ],
            ]);
    }

    #[Test]
    public function test_product_categories_must_be_array(): void
    {
        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => 1200,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
            ],
            'categories' => 'not-an-array',
        ];

        $response = $this->postJson('/api/products', $data);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'categories',
                ],
            ]);
    }

    #[Test]
    public function test_product_categories_must_be_exist(): void
    {
        $data = [
            'name' => [
                'en' => 'Laptop',
                'ar' => 'حاسوب محمول',
            ],
            'description' => [
                'en' => 'Description EN',
                'ar' => 'Description AR',
            ],
            'price' => 1200,
            'images' => [
                UploadedFile::fake()->image('image1.jpg'),
            ],
            'categories' => [1, 2, 3],
        ];

        $response = $this->postJson('/api/products', $data);
        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'categories.1',
                ],
            ]);
    }
}
