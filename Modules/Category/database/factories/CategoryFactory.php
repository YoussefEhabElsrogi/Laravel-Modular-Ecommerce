<?php

namespace Modules\Category\Database\Factories;

use Modules\Category\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => [
                'en' => $this->faker->words(2, true),
                'ar' => $this->faker->words(2, true),
            ],
            'image' => 'categories/' . $this->faker->uuid() . '.jpg',
        ];
    }

    /**
     * Indicate that the category should have an image.
     */
    public function withImage(): static
    {
        return $this->state(fn (array $attributes) => [
            'image' => 'categories/' . $this->faker->uuid() . '.jpg',
        ]);
    }
}
