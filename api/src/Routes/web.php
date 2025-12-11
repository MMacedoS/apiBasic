<?php

use App\Config\Route;
use App\Http\Controllers\v1\Home\HomeController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/ss', function () {
    return ['message' => 'This is a test route'];
});

Route::post('/users', function () {
    // Handler for POST /api/v1/users
});

Route::put('/users/{id}', function ($id) {
    // Handler for PUT /api/v1/users/{id}
});

Route::delete('/users/{id}', function ($id) {
    // Handler for DELETE /api/v1/users/{id}
});
