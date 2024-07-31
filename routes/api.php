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

Route::group(['prefix' => '/racks'], function($route) {
    $route->get('/{id}', [\App\Http\ApiControllers\RackController::class, 'show']);
    $route->get('/', [\App\Http\ApiControllers\RackController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\RackController::class, 'save']);
    $route->delete('/{id}', [\App\Http\ApiControllers\RackController::class, 'delete']);
});

Route::group(['prefix' => '/lockers'], function($route) {
    $route->get('/{id}', [\App\Http\ApiControllers\LockerController::class, 'show']);
    $route->get('/', [\App\Http\ApiControllers\LockerController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\LockerController::class, 'save']);
    $route->delete('/{id}', [\App\Http\ApiControllers\LockerController::class, 'delete']);
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

Route::group(['prefix' => '/receive-orders'], function($route) {
    $route->get('/{id}', [\App\Http\ApiControllers\ReceiveOrderController::class, 'show']);
    $route->get('/', [\App\Http\ApiControllers\ReceiveOrderController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\ReceiveOrderController::class, 'save']);
    $route->delete('/{id}', [\App\Http\ApiControllers\ReceiveOrderController::class, 'delete']);
});

Route::group(['prefix' => '/receive-items'], function($route) {
    $route->get('/{id}', [\App\Http\ApiControllers\ReceiveOrderItemController::class, 'show']);
    $route->get('/', [\App\Http\ApiControllers\ReceiveOrderItemController::class, 'index']);
    $route->post('/', [\App\Http\ApiControllers\ReceiveOrderItemController::class, 'store']);
    $route->delete('/{id}', [\App\Http\ApiControllers\ReceiveOrderItemController::class, 'delete']);
});

