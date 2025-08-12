<?php

use Illuminate\Support\Facades\Route;
use Modules\Category\Http\Controllers\CategoryController;


Route::group(['middleware' => 'setLocale'], function () {
    Route::apiResource('categories', CategoryController::class)->except(['update']);
    Route::post('categories/{category}', [CategoryController::class, 'update']);
});
