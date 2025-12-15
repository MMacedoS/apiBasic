<?php

use App\Config\Route;
use App\Http\Controllers\v1\Employees\FuncionarioController;

Route::group(['middleware' => ['auth', 'role' => 'admin']], function () {
    Route::get('/employees', [FuncionarioController::class, 'index']);
    Route::get('/employees/{uuid}', [FuncionarioController::class, 'show']);
    Route::post('/employees', [FuncionarioController::class, 'store']);
    Route::put('/employees/{uuid}', [FuncionarioController::class, 'update']);
    Route::delete('/employees/{uuid}', [FuncionarioController::class, 'destroy']);
});
