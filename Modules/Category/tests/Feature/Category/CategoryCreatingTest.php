<?php

namespace Modules\Category\Tests\Feature\Category;

use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Category\Models\Category;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryCreatingTest extends TestCase
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
    public function test_create_category()
    {
        $file = UploadedFile::fake()->image('category.jpg');

        $data = [
            'name' => [
                'en' => 'Test Category',
                'ar' => 'اختبار الفئة'
            ],
            'image' => $file,
        ];

        $response = $this->postJson('/api/categories', $data);

        $response
            ->assertStatus(201)
            ->assertJson([
                'status' => true,
                'message' => __('messages.categories.create_success'),
            ]);

        $responseData = $response->json('data');

        $this->assertDatabaseHas(self::TABLE_NAME, [
            'name->en' => 'Test Category',
            'name->ar' => 'اختبار الفئة',
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

    #[Test]
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

    #[Test]
    public function test_check_category_name_must_be_at_most_100_characters_long(): void
    {
        $file = UploadedFile::fake()->image('category.jpg');

        $data = [
            'name' => [
                'en' => str_repeat('a', 101),
                'ar' => str_repeat('a', 101),
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


        $data = [
            'name' => [
                'en' => 'Existing Category',
                'ar' => 'فئة موجودة',
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
                ],
            ]);
    }


    #[Test]
    public function test_check_category_image_is_required(): void
    {
        $data = [
            'name' => [
                'en' => 'Test Category',
                'ar' => 'اختبار الفئة',
            ],
            'image' => null,
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

    #[Test]
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
