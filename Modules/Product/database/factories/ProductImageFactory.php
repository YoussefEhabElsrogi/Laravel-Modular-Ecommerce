<?php

namespace Modules\Product\Database\Factories;

use Modules\Product\Models\ProductImage;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductImageFactory extends Factory
{
    protected $model = ProductImage::class;

    public function definition(): array
    {
        return [
            'image' => $this->faker->uuid() . '.jpg',
        ];
    }
}
