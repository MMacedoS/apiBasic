<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__ . '/src/Routes/web.php';

use App\Core\Core;
use App\Config\Route;

Core::dispatch(Route::getRoutes());
