<?php

use App\Config\Route;
use App\Http\Controllers\v1\Person\PessoaController;

Route::group(['middleware' => ['auth', 'role' => 'admin']], function () {
    Route::get('/person', [PessoaController::class, 'index']);
    Route::get('/person/{uuid}', [PessoaController::class, 'show']);
    Route::post('/person', [PessoaController::class, 'store']);
    Route::put('/person/{uuid}', [PessoaController::class, 'update']);
    Route::delete('/person/{uuid}', [PessoaController::class, 'destroy']);
});
