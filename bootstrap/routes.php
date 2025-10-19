<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Bootstrap Routes
|--------------------------------------------------------------------------
|
| Here is where you can register routes that are loaded during the
| application bootstrap process. These routes are loaded before the
| main application routes.
|
*/

// Load authentication routes
require_once __DIR__ . '/../routes/auth.php';
