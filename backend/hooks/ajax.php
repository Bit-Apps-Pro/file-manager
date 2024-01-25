<?php

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Router\Route;
use BitApps\FM\Http\Controllers\FileManagerController;

if (!\defined('ABSPATH')) {
    exit;
}

Route::middleware('nonce')->match(['get', 'post'], 'connector', [FileManagerController::class, 'connector']);
Route::middleware('nonce')->noAuth()->match(['get', 'post'], 'connector_front', [FileManagerController::class, 'connector']);
Route::middleware('nonce')->post('theme', [FileManagerController::class, 'changeThemes']);
