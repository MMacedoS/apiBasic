<?php

use App\Config\Route;
use App\Http\Controllers\v1\Home\HomeController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/health', function () {
    return ['message' => 'This API is healthy'];
});

require_once __DIR__ . '/Users/userRoutes.php';
require_once __DIR__ . '/Services/serviceRoute.php';
require_once __DIR__ . '/Person/personRoute.php';
require_once __DIR__ . '/Customer/customerRoute.php';
require_once __DIR__ . '/Employees/employeesRoute.php';
