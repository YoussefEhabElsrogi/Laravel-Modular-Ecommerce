<?php

namespace Modules\Product\Database\Factories;

use Modules\Product\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Category\Models\Category;
use Modules\Product\Models\ProductImage;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'name' => [
                'en' => $this->faker->words(2, true),
                'ar' => $this->faker->words(2, true),
            ],
            'description' => [
                'en' => $this->faker->sentence(),
                'ar' => $this->faker->sentence(),
            ],
            'price' => $this->faker->randomFloat(2, 10, 1000),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Product $product) {
            // Add related images
            ProductImage::factory()->count(2)->create([
                'product_id' => $product->id,
            ]);

            // Attach related categories
            $categories = Category::factory()->count(2)->create();
            $product->categories()->attach($categories->pluck('id')->toArray());
        });
    }
}
