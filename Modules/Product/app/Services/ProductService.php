<?php

namespace Modules\Product\Services;

use Modules\Product\Models\Product;
use Modules\Product\Models\ProductImage;
use App\Utils\ImageManager;

class ProductService
{
    public function all()
    {
        return Product::with('images', 'categories')->get();
    }

    public function store(array $data)
    {
        $images = $data['images'] ?? [];
        $categories = $data['categories'] ?? [];

        unset($data['images'], $data['categories']);

        $product = Product::create($data);

        if (!empty($images) && is_array($images)) {
            ImageManager::uploadMultiple($images, $product, 'products', 'images');
        }

        if (!empty($categories) && is_array($categories)) {
            $product->categories()->attach($categories);
        }

        return $product->load('images', 'categories');
    }

    public function find(int $id)
    {
        return Product::with('images', 'categories')->findOrFail($id);
    }

    public function update(array $data, Product $product)
    {
        $images = $data['images'] ?? [];
        $categories = $data['categories'] ?? [];

        unset($data['images'], $data['categories']);

        if (!empty($images)) {
            // Delete existing images
            foreach ($product->images as $image) {
                ImageManager::delete($image->image, 'products');
                $image->delete();
            }

            // Upload new images
            ImageManager::uploadMultiple($images, $product, 'products', 'images');
        }

        if (!empty($categories)) {
            $product->categories()->sync($categories);
        }

        $product->update($data);

        return $product->load('images', 'categories');
    }

    public function destroy(Product $product)
    {
        // Delete all images
        foreach ($product->images as $image) {
            ImageManager::delete($image->image, 'products');
            $image->delete();
        }

        // Detach categories
        $product->categories()->detach();

        return $product->delete();
    }
}
