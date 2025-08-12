<?php

namespace Modules\Category\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Category\Models\Category;

class CategoryDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample categories
        $categories = [
            [
                'name' => [
                    'en' => 'Electronics',
                    'ar' => 'إلكترونيات'
                ],
                'image' => 'categories/electronics.jpg'
            ],
            [
                'name' => [
                    'en' => 'Clothing',
                    'ar' => 'ملابس'
                ],
                'image' => 'categories/clothing.jpg'
            ],
            [
                'name' => [
                    'en' => 'Books',
                    'ar' => 'كتب'
                ],
                'image' => 'categories/books.jpg'
            ],
            [
                'name' => [
                    'en' => 'Home & Garden',
                    'ar' => 'المنزل والحديقة'
                ],
                'image' => 'categories/home-garden.jpg'
            ],
            [
                'name' => [
                    'en' => 'Sports',
                    'ar' => 'رياضة'
                ],
                'image' => 'categories/sports.jpg'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
