<?php

namespace Modules\Product\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Product\Models\Product;
use Modules\Product\Services\ProductService;
use Modules\Product\Http\Requests\{ProductStoringRequest, ProductUpdatingRequest};
use Modules\Product\Http\Resources\ProductResource;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    /**
     * @OA\Get(
     *     path="/products",
     *     summary="Get all products",
     *     description="Retrieve a list of all products with their images and categories",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Products retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(
     *                         property="name",
     *                         type="object",
     *                         @OA\Property(property="en", type="string", example="Gaming Laptop"),
     *                         @OA\Property(property="ar", type="string", example="حاسوب محمول للألعاب")
     *                     ),
     *                     @OA\Property(
     *                         property="description",
     *                         type="object",
     *                         @OA\Property(property="en", type="string", example="High-performance gaming laptop"),
     *                         @OA\Property(property="ar", type="string", example="حاسوب محمول عالي الأداء للألعاب")
     *                     ),
     *                     @OA\Property(property="price", type="number", format="float", example=1299.99),
     *                     @OA\Property(
     *                         property="images",
     *                         type="array",
     *                         @OA\Items(type="string", example="http://localhost/uploads/products/image1.jpg")
     *                     ),
     *                     @OA\Property(
     *                         property="categories",
     *                         type="array",
     *                         @OA\Items(
     *                             @OA\Property(property="id", type="integer", example=1),
     *                             @OA\Property(
     *                                 property="name",
     *                                 type="object",
     *                                 @OA\Property(property="en", type="string", example="Electronics"),
     *                                 @OA\Property(property="ar", type="string", example="إلكترونيات")
     *                             ),
     *                             @OA\Property(property="image", type="string", example="http://localhost/uploads/categories/category1.jpg"),
     *                             @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01 00:00:00"),
     *                             @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01 00:00:00")
     *                         )
     *                     ),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01 00:00:00"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01 00:00:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $products = ProductResource::collection($this->productService->all());
        return ApiResponse::success($products, __('messages.products.list_success'));
    }

    /**
     * @OA\Post(
     *     path="/products",
     *     summary="Create a new product",
     *     description="Create a new product with multilingual content, images, and category associations. All fields are required for product creation.",
     *     operationId="createProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="lang",
     *         in="query",
     *         required=false,
     *         @OA\Schema(type="string", enum={"en", "ar"})
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Product creation data including multilingual content, images, and categories",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name[en]", "name[ar]", "description[en]", "description[ar]", "price", "images", "categories"},
     *                 @OA\Property(
     *                     property="name[en]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=100,
     *                     description="Product name in English (required, 3-100 characters)",
     *                     example="Gaming Laptop"
     *                 ),
     *                 @OA\Property(
     *                     property="name[ar]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=100,
     *                     description="Product name in Arabic (required, 3-100 characters)",
     *                     example="حاسوب محمول للألعاب"
     *                 ),
     *                 @OA\Property(
     *                     property="description[en]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=1000,
     *                     description="Product description in English (required, 3-1000 characters)",
     *                     example="High-performance gaming laptop with advanced graphics and fast processor"
     *                 ),
     *                 @OA\Property(
     *                     property="description[ar]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=1000,
     *                     description="Product description in Arabic (required, 3-1000 characters)",
     *                     example="حاسوب محمول عالي الأداء للألعاب مع رسومات متقدمة ومعالج سريع"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     format="float",
     *                     minimum=0,
     *                     description="Product price (required, must be 0 or greater)",
     *                     example=1299.99
     *                 ),
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     minItems=1,
     *                     description="Product images (required, at least one image)",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Image file (jpeg, png, jpg, gif, svg)"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="categories[]",
     *                     type="array",
     *                     minItems=1,
     *                     description="Category IDs (required, at least one category)",
     *                     @OA\Items(
     *                         type="integer",
     *                         description="Valid category ID",
     *                         example=1
     *                     ),
     *                     example={1, 2}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="name",
     *                     type="object",
     *                     description="Multilingual product name",
     *                     @OA\Property(property="en", type="string", example="Gaming Laptop"),
     *                     @OA\Property(property="ar", type="string", example="حاسوب محمول للألعاب")
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="object",
     *                     description="Multilingual product description",
     *                     @OA\Property(property="en", type="string", example="High-performance gaming laptop with advanced graphics and fast processor"),
     *                     @OA\Property(property="ar", type="string", example="حاسوب محمول عالي الأداء للألعاب مع رسومات متقدمة ومعالج سريع")
     *                 ),
     *                 @OA\Property(property="price", type="number", format="float", example=1299.99),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Array of uploaded image URLs",
     *                     @OA\Items(type="string", example="http://localhost/uploads/products/image1.jpg")
     *                 ),
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     description="Associated categories",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(
     *                             property="name",
     *                             type="object",
     *                             @OA\Property(property="en", type="string", example="Electronics"),
     *                             @OA\Property(property="ar", type="string", example="إلكترونيات")
     *                         ),
     *                         @OA\Property(property="image", type="string", example="http://localhost/uploads/categories/category1.jpg"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01 00:00:00"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01 00:00:00")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01 00:00:00"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01 00:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Validation errors for each field",
     *                 @OA\Property(
     *                     property="name.en",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name.en field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="array",
     *                     @OA\Items(type="string", example="The price must be a number.")
     *                 ),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(type="string", example="The images field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="categories.0",
     *                     type="array",
     *                     @OA\Items(type="string", example="The selected categories.0 is invalid.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to create product due to server error")
     *         )
     *     )
     * )
     */
    public function store(ProductStoringRequest $request): JsonResponse
    {
        $product = new ProductResource($this->productService->store($request->validated()));

        return ApiResponse::success($product, __('messages.products.create_success'), 201);
    }

    /**
     * @OA\Get(
     *     path="/products/{id}",
     *     summary="Get a specific product",
     *     description="Retrieve details of a specific product by ID",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Parameter(
     *             name="lang",
     *             in="query",
     *             required=false,
     *             @OA\Schema(type="string", enum={"en", "ar"})
     *         ),
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="name",
     *                     type="object",
     *                     @OA\Property(property="en", type="string", example="Gaming Laptop"),
     *                     @OA\Property(property="ar", type="string", example="حاسوب محمول للألعاب")
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="object",
     *                     @OA\Property(property="en", type="string", example="High-performance gaming laptop"),
     *                     @OA\Property(property="ar", type="string", example="حاسوب محمول عالي الأداء للألعاب")
     *                 ),
     *                 @OA\Property(property="price", type="number", format="float", example=1299.99),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     @OA\Items(type="string", example="http://localhost/uploads/products/image1.jpg")
     *                 ),
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Electronics")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     )
     * )
     */
    public function show(Product $product): JsonResponse
    {
        $productResource = new ProductResource($product->load('images', 'categories'));
        return ApiResponse::success($productResource, __('messages.products.show_success'));
    }

    /**
     * @OA\Post(
     *     path="/products/{id}",
     *     summary="Update a product",
     *     description="Update an existing product with new multilingual content, images, and categories. All fields are required for update.",
     *     operationId="updateProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID to update",
     *         required=true,
     *         @OA\Parameter(
     *             name="lang",
     *             in="query",
     *             required=false,
     *             @OA\Schema(type="string", enum={"en", "ar"})
     *         ),
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated product data including multilingual content, optional images, and categories",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name[en]", "name[ar]", "description[en]", "description[ar]", "price"},
     *                 @OA\Property(
     *                     property="name[en]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=100,
     *                     description="Updated product name in English (required, 3-100 characters)",
     *                     example="Updated Gaming Laptop Pro"
     *                 ),
     *                 @OA\Property(
     *                     property="name[ar]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=100,
     *                     description="Updated product name in Arabic (required, 3-100 characters)",
     *                     example="حاسوب محمول محدث للألعاب المحترف"
     *                 ),
     *                 @OA\Property(
     *                     property="description[en]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=1000,
     *                     description="Updated product description in English (required, 3-1000 characters)",
     *                     example="Enhanced high-performance gaming laptop with latest graphics and ultra-fast processor"
     *                 ),
     *                 @OA\Property(
     *                     property="description[ar]",
     *                     type="string",
     *                     minLength=3,
     *                     maxLength=1000,
     *                     description="Updated product description in Arabic (required, 3-1000 characters)",
     *                     example="حاسوب محمول محسن عالي الأداء للألعاب مع أحدث الرسومات ومعالج فائق السرعة"
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="number",
     *                     format="float",
     *                     minimum=0,
     *                     description="Updated product price (required, must be 0 or greater)",
     *                     example=1599.99
     *                 ),
     *                 @OA\Property(
     *                     property="images[]",
     *                     type="array",
     *                     description="New product images (optional, replaces all existing images if provided)",
     *                     @OA\Items(
     *                         type="string",
     *                         format="binary",
     *                         description="Image file (jpeg, png, jpg, gif, svg)"
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="categories[]",
     *                     type="array",
     *                     description="Updated category IDs (optional, replaces all existing categories if provided)",
     *                     @OA\Items(
     *                         type="integer",
     *                         description="Valid category ID",
     *                         example=1
     *                     ),
     *                     example={1, 3, 5}
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(
     *                     property="name",
     *                     type="object",
     *                     description="Updated multilingual product name",
     *                     @OA\Property(property="en", type="string", example="Updated Gaming Laptop Pro"),
     *                     @OA\Property(property="ar", type="string", example="حاسوب محمول محدث للألعاب المحترف")
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="object",
     *                     description="Updated multilingual product description",
     *                     @OA\Property(property="en", type="string", example="Enhanced high-performance gaming laptop with latest graphics and ultra-fast processor"),
     *                     @OA\Property(property="ar", type="string", example="حاسوب محمول محسن عالي الأداء للألعاب مع أحدث الرسومات ومعالج فائق السرعة")
     *                 ),
     *                 @OA\Property(property="price", type="number", format="float", example=1599.99),
     *                 @OA\Property(
     *                     property="images",
     *                     type="array",
     *                     description="Updated image URLs",
     *                     @OA\Items(type="string", example="http://localhost/uploads/products/updated_image1.jpg")
     *                 ),
     *                 @OA\Property(
     *                     property="categories",
     *                     type="array",
     *                     description="Updated associated categories",
     *                     @OA\Items(
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(
     *                             property="name",
     *                             type="object",
     *                             @OA\Property(property="en", type="string", example="Electronics"),
     *                             @OA\Property(property="ar", type="string", example="إلكترونيات")
     *                         ),
     *                         @OA\Property(property="image", type="string", example="http://localhost/uploads/categories/category1.jpg"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01 00:00:00"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01 00:00:00")
     *                     )
     *                 ),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2025-01-01 00:00:00"),
     *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2025-01-01 12:00:00")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 description="Validation errors for each field",
     *                 @OA\Property(
     *                     property="name.en",
     *                     type="array",
     *                     @OA\Items(type="string", example="The name.en field is required.")
     *                 ),
     *                 @OA\Property(
     *                     property="price",
     *                     type="array",
     *                     @OA\Items(type="string", example="The price must be a number.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to update product due to server error")
     *         )
     *     )
     * )
     */
    public function update(ProductUpdatingRequest $request, Product $product): JsonResponse
    {
        $updatedProduct = new ProductResource($this->productService->update($request->validated(), $product));

        return ApiResponse::success($updatedProduct, __('messages.products.update_success'));
    }

    /**
     * @OA\Delete(
     *     path="/products/{id}",
     *     summary="Delete a product",
     *     description="Permanently delete an existing product and all its associated data including images, categories associations, and related records. This action cannot be undone.",
     *     operationId="deleteProduct",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Product ID to delete",
     *         required=true,
     *         @OA\Parameter(
     *             name="lang",
     *             in="query",
     *             required=false,
     *             @OA\Schema(type="string", enum={"en", "ar"})
     *         ),
     *         @OA\Schema(
     *             type="integer",
     *             minimum=1,
     *             example=1
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Product deleted successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 description="Empty array indicating successful deletion",
     *                 @OA\Items(),
     *                 example={}
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Product not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Product not found"),
     *             @OA\Property(property="data", type="null", example=null)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to delete product due to server error")
     *         )
     *     )
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->destroy($product);

        return ApiResponse::success([], __('messages.products.delete_success'));
    }
}
