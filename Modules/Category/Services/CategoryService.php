<?php

namespace Modules\Category\Services;

use Modules\Category\Models\Category;
use Illuminate\Http\UploadedFile;
use App\Utils\ImageManager;

class CategoryService
{
    public function all()
    {
        return Category::all();
    }

    public function store(array $data)
    {
        $this->handleImageUpload($data);
        return Category::create($data);
    }

    public function find(int $id)
    {
        return Category::findOrFail($id);
    }

    public function update(array $data, Category $category)
    {
        $this->handleImageUpload($data, $category->image);
        $category->update($data);
        return $category;
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            ImageManager::delete($category->image, 'categories');
        }
        return $category->delete();
    }

    private function handleImageUpload(array &$data, ?string $oldImage = null): void
    {
        if (isset($data['image']) && $data['image'] instanceof UploadedFile) {
            if ($oldImage) {
                ImageManager::delete($oldImage, 'categories');
            }
            $data['image'] = ImageManager::uploadSingle($data['image'], 'categories');
        }
    }
}
