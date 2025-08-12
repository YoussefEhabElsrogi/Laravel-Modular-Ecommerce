<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;


Route::group(['middleware' => 'setLocale'], function () {
    Route::apiResource('products', ProductController::class)->except(['update']);
    Route::post('products/{product}', [ProductController::class, 'update']);
});
