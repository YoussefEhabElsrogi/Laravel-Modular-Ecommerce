<?php

namespace Modules\Category\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Helpers\ApiResponse;
use Modules\Category\Http\Requests\{
    CategoryStoringRequest,
    CategoryUpdatingRequest
};
use Modules\Category\Http\Resources\CategoryResource;
use App\Http\Controllers\Controller;
use Modules\Category\Models\Category;
use Modules\Category\Services\CategoryService;

/**
 * @OA\Tag(
 *     name="Categories",
 *     description="API Endpoints for managing categories"
 * )
 */
class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService) {}

    /**
     * @OA\Get(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Get list of categories",
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Categories retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Category")
     *             ),
     *             example={
     *                 "status": true,
     *                 "message": "Categories retrieved successfully.",
     *                 "data": {
     *                     {
     *                         "id": 1,
     *                         "name": "Electronics",
     *                         "created_at": "2025-08-11T10:00:00Z",
     *                         "updated_at": "2025-08-11T10:00:00Z"
     *                     },
     *                     {
     *                         "id": 2,
     *                         "name": "Books",
     *                         "created_at": "2025-08-11T10:05:00Z",
     *                         "updated_at": "2025-08-11T10:05:00Z"
     *                     }
     *                 }
     *             }
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $categories = CategoryResource::collection($this->categoryService->all());
        return ApiResponse::success($categories, __('messages.categories.list_success'));
    }


    /**
     * @OA\Get(
     *     path="/api/categories/{id}",
     *     tags={"Categories"},
     *     summary="Get category details by ID",
     *     description="Retrieve a single category by its unique ID.",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The ID of the category",
     *         required=true,
     *         @OA\Schema(type="integer", example=6)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="تم استرجاع الفئة بنجاح."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 example={
     *                     "id": 6,
     *                     "name": "إلكترونيات",
     *                     "image": "http://localhost:8000/uploads/categories/categories/electronics.jpg",
     *                     "created_at": "2025-08-11 18:04:38",
     *                     "updated_at": "2025-08-11 18:04:38"
     *                 }
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Category not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="الفئة غير موجودة."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"), example={})
     *         )
     *     )
     * )
     */

    public function show(Category $category): JsonResponse
    {
        $category = new CategoryResource($this->categoryService->find($category->id));
        return ApiResponse::success($category, __('messages.categories.show_success'));
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Create a new category",
     *     description="Create a category with English and Arabic names and an image.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="object",
     *                 @OA\Property(property="en", type="string", example="Electronics"),
     *                 @OA\Property(property="ar", type="string", example="إلكترونيات")
     *             ),
     *             @OA\Property(
     *                 property="image",
     *                 type="file",
     *                 format="binary",
     *                 example="category.jpg"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="object",
     *                 @OA\Property(property="en", type="string", example="Electronics"),
     *                 @OA\Property(property="ar", type="string", example="إلكترونيات")
     *             ),
     *             @OA\Property(property="image", type="string", example="https://example.com/images/category.jpg"),
     *             @OA\Property(property="created_at", type="string", format="date-time", example="2025-08-11T12:00:00Z"),
     *             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-08-11T12:00:00Z")
     *         )
     *     )
     * )
     */

    public function store(CategoryStoringRequest $request): JsonResponse
    {
        $category = new CategoryResource($this->categoryService->store($request->validated()));

        return ApiResponse::success($category, __('messages.categories.create_success'), 201);
    }

    /**
     * @OA\Post(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Update a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="name",
     *                 type="object",
     *                 @OA\Property(property="en", type="string", example="Toys"),
     *                 @OA\Property(property="ar", type="string", example="ألعاب")
     *             ),
     *             @OA\Property(
     *                 property="image",
     *                 type="string",
     *                 format="binary",
     *                 description="Image file or URL"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Category updated successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=26),
     *                 @OA\Property(property="name", type="string", example="Toys"),
     *                 @OA\Property(property="image", type="string", example="http://localhost:8000/uploads/categories/Image_c66d33aa-4655-46eb-a7b0-8cdb08572c2b_1754934136.png"),
     *                 @OA\Property(property="created_at", type="string", example="2025-08-11 17:35:02"),
     *                 @OA\Property(property="updated_at", type="string", example="2025-08-11 17:42:16")
     *             )
     *         )
     *     )
     * )
     */

    public function update(CategoryUpdatingRequest $request, Category $category): JsonResponse
    {
        $category = new CategoryResource($this->categoryService->update($request->validated(), $category));
        return ApiResponse::success($category, __('messages.categories.update_success'));
    }

    /**
     * @OA\Delete(
     *     path="/categories/{id}",
     *     tags={"Categories"},
     *     summary="Delete a category",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Category ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Category deleted successfully."),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->destroy($category);
        return ApiResponse::success([], __('messages.categories.delete_success'));
    }
}
