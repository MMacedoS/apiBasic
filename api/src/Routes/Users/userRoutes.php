<?php

use App\Config\Route;
use App\Http\Controllers\v1\Users\UserController;

Route::post('/login', [UserController::class, 'authenticate']);
Route::get('/confirm-email/{token}', [UserController::class, 'confirmEmail']);

Route::group(['middleware' => ['auth', 'role' => 'admin']], function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'profileUpdate']);
});
