<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');


Route::get('/products', [\App\Http\ApiControllers\ProductController::class, 'index']);
Route::post('/products', [\App\Http\ApiControllers\ProductController::class, 'save']);
Route::get('/products/{id}', [\App\Http\ApiControllers\ProductController::class, 'show']);
Route::delete('/products/{id}', [\App\Http\ApiControllers\ProductController::class, 'delete']);
