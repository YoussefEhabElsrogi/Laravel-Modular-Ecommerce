<?php

namespace Tests\Feature\Categories;

use Modules\Category\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryRetrievingTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function test_check_if_retrieving_categories_is_success(): void
    {
        // Arrange
        Category::factory()->count(10)->create();

        // Act
        $response = $this->getJson('/api/categories');

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
                        'image',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ])
            ->assertJsonCount(10, 'data');
    }


    #[Test]
    public function test_check_if_show_category_is_success(): void
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->getJson('/api/categories/' . $category->id);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'image',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
