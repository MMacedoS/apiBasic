<?php

use App\Config\Route;
use App\Http\Controllers\v1\Customers\ClienteController;

Route::group(
    [
        'middleware' => [
            'auth',
            'permission' => [
                'create-customer',
                'view-customer',
                'update-customer',
                'delete-customer'
            ]
        ]
    ],
    function () {
        Route::get('/customers', [ClienteController::class, 'index']);
        Route::get('/customers/{uuid}', [ClienteController::class, 'show']);
        Route::post('/customers', [ClienteController::class, 'store']);
        Route::put('/customers/{uuid}', [ClienteController::class, 'update']);
        Route::delete('/customers/{uuid}', [ClienteController::class, 'destroy']);
    }
);
