<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     title="Laravel Modular E-commerce API",
 *     version="1.0.0",
 *     description="This is the API documentation for the Laravel Modular E-commerce API."
 * )
 *
 * @OA\Server(
 *     url="http://localhost:8000/api",
 *     description="Local API server"
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     title="Category",
 *     description="Category model",
 *     @OA\Property(property="id", type="integer", description="Category ID"),
 *     @OA\Property(property="name", type="string", description="Category name"),
 *     @OA\Property(property="image", type="string", description="Category image URL"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="Created timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="Updated timestamp")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryStoreRequest",
 *     type="object",
 *     title="Category Store Request",
 *     description="Request body for creating a category",
 *     required={"name", "image"},
 *     @OA\Property(
 *         property="name",
 *         type="object",
 *         description="Category name in different languages",
 *         @OA\Property(property="en", type="string", description="English name"),
 *         @OA\Property(property="ar", type="string", description="Arabic name")
 *     ),
 *     @OA\Property(property="image", type="string", format="binary", description="Category image file")
 * )
 *
 * @OA\Schema(
 *     schema="CategoryUpdateRequest",
 *     type="object",
 *     title="Category Update Request",
 *     description="Request body for updating a category",
 *     @OA\Property(
 *         property="name",
 *         type="object",
 *         description="Category name in different languages",
 *         @OA\Property(property="en", type="string", description="English name"),
 *         @OA\Property(property="ar", type="string", description="Arabic name")
 *     ),
 *     @OA\Property(property="image", type="string", format="binary", description="Category image file")
 * )
 */
abstract class Controller
{
    //
}
