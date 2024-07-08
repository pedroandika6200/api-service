<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function (Request $request) {
    return response()->json([
        'app' => env('APP_NAME'),
        'version' => "v0.0",
        'Build' => "Laravel v". app()->version(),
    ]);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth');


Route::get('/test', function (Request $request) {
    $event = \App\Events\RecordSaved::dispatch('xx-key', \App\Models\SalesOrder::first());
    return response()->json($event);
});


Route::group(['prefix' => '/product-categories'], function($route) {
    $route->get('/{id}', [\App\Http\ApiControllers\ProductCategoryController::class, 'show']);
    $route->get('/', [\App\Http\ApiControllers\ProductCategoryController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\ProductCategoryController::class, 'save']);
    $route->delete('/{id}', [\App\Http\ApiControllers\ProductCategoryController::class, 'delete']);
});

Route::group(['prefix' => '/products'], function($route) {
    $route->get('/{id}', [\App\Http\ApiControllers\ProductController::class, 'show']);
    $route->get('/', [\App\Http\ApiControllers\ProductController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\ProductController::class, 'save']);
    $route->delete('/{id}', [\App\Http\ApiControllers\ProductController::class, 'delete']);
});

Route::group(['prefix' => '/customers'], function($route) {
    $route->get('/{id}', [\App\Http\ApiControllers\CustomerController::class, 'show']);
    $route->get('/', [\App\Http\ApiControllers\CustomerController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\CustomerController::class, 'save']);
    $route->delete('/{id}', [\App\Http\ApiControllers\CustomerController::class, 'delete']);
});

Route::group(['prefix' => '/sales-orders'], function($route) {
    $route->post('/{id}/order-approved', [\App\Http\ApiControllers\SalesOrderController::class, 'orderApproved']);

    $route->get('/{id}', [\App\Http\ApiControllers\SalesOrderController::class, 'show']);
    $route->delete('/{id}', [\App\Http\ApiControllers\SalesOrderController::class, 'delete']);
    $route->get('/', [\App\Http\ApiControllers\SalesOrderController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\SalesOrderController::class, 'save']);
});
