<?php

use App\Config\Route;
use App\Http\Controllers\v1\Products\ProdutoController;

Route::group(['middleware' => ['auth', 'role' => 'admin']], function () {
    Route::get('/products', [ProdutoController::class, 'index']);
    Route::get('/products/{uuid}', [ProdutoController::class, 'show']);
    Route::post('/products', [ProdutoController::class, 'store']);
    Route::put('/products/{uuid}', [ProdutoController::class, 'update']);
    Route::delete('/products/{uuid}', [ProdutoController::class, 'destroy']);
});

Route::group(['middleware' => ['auth', 'permission' => [
    'view_products'
]]], function () {
    Route::get('/products-list', [ProdutoController::class, 'indexWithoutPagination']);
});
