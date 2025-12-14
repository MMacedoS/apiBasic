<?php

use App\Config\Route;
use App\Http\Controllers\v1\Customers\ClienteController;

Route::group(['middleware' => ['auth']], function () {
    Route::get('/customer', [ClienteController::class, 'index']);
    Route::get('/customer/{uuid}', [ClienteController::class, 'show']);
    Route::post('/customer', [ClienteController::class, 'store']);
    Route::put('/customer/{uuid}', [ClienteController::class, 'update']);
    Route::delete('/customer/{uuid}', [ClienteController::class, 'destroy']);
});
