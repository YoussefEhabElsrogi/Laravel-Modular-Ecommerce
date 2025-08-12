<?php

namespace App\Utils;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class ImageManager
{
    /**
     * Upload multiple images and attach them to a model relation.
     *
     * @param array $images
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $disk
     * @param string $relation
     * @return void
     */
    public static function uploadMultiple(array $images, $model, string $disk = 'public', string $relation = 'images'): void
    {
        foreach ($images as $image) {
            $fileName = self::generateImageName($image);
            self::store($image, '/', $fileName, $disk);

            $model->$relation()->create([
                'image' => $fileName,
            ]);
        }
    }

    /**
     * Upload a single image.
     *
     * @param UploadedFile $image
     * @param string $path
     * @param string $disk
     * @return string
     */
    public static function uploadSingle(UploadedFile $image, string $disk = 'public', string $path = '/'): string
    {
        $fileName = self::generateImageName($image);
        self::store($image, $path, $fileName, $disk);
        return $fileName;
    }

    /**
     * Generate a unique image name with extension.
     *
     * @param UploadedFile $image
     * @return string
     */
    public static function generateImageName(UploadedFile $image): string
    {
        $extension = $image->getClientOriginalExtension();

        $uuid = Str::uuid()->toString();

        $timestamp = time();

        return "Image_{$uuid}_{$timestamp}.{$extension}";
    }

    /**
     * Store the image using Laravel Storage.
     *
     * @param UploadedFile $image
     * @param string $path
     * @param string $fileName
     * @param string $disk
     * @return void
     */
    public static function store(UploadedFile $image, string $path, string $fileName, string $disk = 'public'): void
    {
        $image->storeAs($path, $fileName, ['disk' => $disk]);
    }

    /**
     * Delete an image from storage.
     *
     * @param string $path
     * @param string $disk
     * @return void
     */
    public static function delete(string $oldImagePath, string $disk = 'public'): void
    {
        if (Storage::disk($disk)->exists($oldImagePath)) {
            Storage::disk($disk)->delete($oldImagePath);
        }
    }
}
