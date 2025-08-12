<?php

namespace Modules\Category\Tests\Feature\Category;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryUpdatingTest extends TestCase
{
    use RefreshDatabase;

    const TABLE_NAME = 'categories';

    public function setUp(): void
    {
        parent::setUp();

        // Delete all files in the upload directory
        $uploadPath = public_path('uploads/categories');
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
    public function test_update_category()
    {
        // Arrange
        $category = Category::factory()->create();

        $updatedCategory = [
            'name' => [
                'en' => 'Updated Test Category',
                'ar' => 'اختبار الفئة المحدثة',
            ],
            'image' => UploadedFile::fake()->image('category.jpg'),
        ];

        // Act
        $response = $this->postJson('/api/categories/' . $category->id, $updatedCategory);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.categories.update_success'),
            ]);

        $responseData = $response->json('data');

        $this->assertDatabaseHas(self::TABLE_NAME, [
            'name->en' => $updatedCategory['name']['en'],
            'name->ar' => $updatedCategory['name']['ar'],
            'image' => basename($responseData['image']),
        ]);

        $this->assertFileExists(storage_path('app/public/uploads/categories/' . basename($responseData['image'])));
    }

    #[Test]
    public function test_category_name_is_required()
    {
        $file = UploadedFile::fake()->image('category.jpg');

        $data = [
            'name' => [
                'en' => '',
                'ar' => '',
            ],
            'image' => $file,
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name.en',
                    'name.ar',
                ]
            ]);

        $this->assertDatabaseMissing(self::TABLE_NAME, $data);
    }

    // #[Test]
    public function test_check_category_name_must_be_at_least_3_characters_long(): void
    {
        $file = UploadedFile::fake()->image('category.jpg');

        $data = [
            'name' => [
                'en' => 'ab',
                'ar' => 'ab',
            ],
            'image' => $file,
        ];

        $response = $this->postJson('/api/categories', $data);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name.en',
                    'name.ar',
                ]
            ]);

        $this->assertDatabaseMissing(self::TABLE_NAME, $data);
    }

    // #[Test]
    public function test_check_category_name_must_be_at_most_255_characters_long(): void
    {
        $file = UploadedFile::fake()->image('category.jpg');

        $data = [
            'name' => [
                'en' => str_repeat('a', 256),
                'ar' => str_repeat('a', 256),
            ],
            'image' => $file,
        ];

        $response = $this->postJson('/api/categories', $data);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name.en',
                    'name.ar',
                ]
            ]);

        $this->assertDatabaseMissing(self::TABLE_NAME, $data);
    }

    #[Test]
    public function test_check_category_name_must_be_unique(): void
    {
        $file = UploadedFile::fake()->image('category.jpg');

        $existingCategory = Category::factory()->create([
            'name' => [
                'en' => 'Existing Category',
                'ar' => 'فئة موجودة',
            ],
            'image' => 'existing-image.jpg',
        ]);

        $categoryToUpdate = Category::factory()->create([
            'name' => [
                'en' => 'Category to Update',
                'ar' => 'فئة للتحديث',
            ],
            'image' => 'update-image.jpg',
        ]);

        $data = [
            'name' => [
                'en' => 'Existing Category',
                'ar' => 'فئة موجودة',
            ],
            'image' => $file,
        ];

        $response = $this->postJson('/api/categories/' . $categoryToUpdate->id, $data);

        $response
            ->assertStatus(422)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'name.en',
                    'name.ar',
                ],
            ]);
    }


    // #[Test]
    public function test_check_category_image_is_valid(): void
    {
        $data = [
            'name' => [
                'en' => 'Test Category',
                'ar' => 'اختبار الفئة',
            ],
            'image' => UploadedFile::fake()->create('invalid-image.txt'),
        ];

        $response = $this->postJson('/api/categories', $data);

        $response->assertStatus(422)->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'image',
            ]
        ]);
    }
}
