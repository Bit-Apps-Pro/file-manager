<?php

use BitApps\FM\Core\Http\Router\Route;
use BitApps\FM\Http\Controllers\FileManagerController;
use BitApps\FM\Providers\FileManager;

if (!\defined('ABSPATH')) {
    exit;
}

Route::middleware('nonce')->match(['get', 'post'], 'connector', [FileManagerController::class, 'connector']);
Route::middleware('nonce')->post('theme', [FileManagerController::class, 'changeTheme']);
