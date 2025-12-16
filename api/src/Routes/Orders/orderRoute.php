<?php

use App\Config\Route;
use App\Http\Controllers\v1\Orders\OrdemController;

Route::group(['middleware' => ['auth', 'role' => 'admin']], function () {
    Route::get('/orders', [OrdemController::class, 'index']);
    Route::get('/orders/{uuid}', [OrdemController::class, 'show']);
    Route::post('/orders', [OrdemController::class, 'store']);
    Route::put('/orders/{uuid}', [OrdemController::class, 'update']);
    Route::delete('/orders/{uuid}', [OrdemController::class, 'destroy']);
});

Route::group(['middleware' => ['auth', 'permission' => [
    'view_orders',
    'view-own-order',
    'assign-order',
    'remove-order',
    'view-my-order',
    'view-my-service',
    'view-my-customer'
]]], function () {
    Route::get('/orders-list', [OrdemController::class, 'indexWithoutPagination']);
    Route::post('/orders/{uuid}/assign-service', [OrdemController::class, 'assignServiceToOrder']);
    Route::delete('/orders/{uuid}/remove-service/{uuidService}', [OrdemController::class, 'removeServiceFromOrder']);
    Route::post('/orders/{uuid}/assign-product', [OrdemController::class, 'assignProductToOrder']);
    Route::delete('/orders/{uuid}/remove-product/{uuidProduct}', [OrdemController::class, 'removeProductFromOrder']);
});
