<?php

use App\Config\Route;
use App\Http\Controllers\v1\Sales\VendaController;

Route::group(
    [
        'middleware' => ['auth', 'role' => 'padrao'],
    ],
    function () {
        Route::get('/sales', [VendaController::class, 'index']);
        Route::post('/sales', [VendaController::class, 'store']);
        Route::get('/sales/{uuid}', [VendaController::class, 'show']);
        Route::put('/sales/{uuid}', [VendaController::class, 'update']);
        Route::delete('/sales/{uuid}', [VendaController::class, 'destroy']);
    }
);
