<?php

use App\Config\Route;
use App\Http\Controllers\v1\Home\HomeController;
use App\Http\Controllers\v1\Users\UserController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/health', function () {
    return ['message' => 'This API is healthy'];
});

Route::get('/users', [UserController::class, 'index']);

Route::post('/users', function () {
    // Handler for POST /api/v1/users
});

Route::put('/users/{id}', function ($id) {
    // Handler for PUT /api/v1/users/{id}
});

Route::delete('/users/{id}', function ($id) {
    // Handler for DELETE /api/v1/users/{id}
});
