<?php

namespace Tests\Feature\Categories;

use Modules\Category\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CategoryDeletingTest extends TestCase
{
    use RefreshDatabase;

    const TABLE_NAME = 'categories';


    #[Test]
    public function test_delete_category(): void
    {
        // Arrange
        $category = Category::factory()->create();

        // Act
        $response = $this->deleteJson('/api/categories/' . $category->id);

        // Assert
        $response
            ->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => __('messages.categories.delete_success'),
            ]);

        $this->assertDatabaseMissing(self::TABLE_NAME, $category->toArray());
    }
}
