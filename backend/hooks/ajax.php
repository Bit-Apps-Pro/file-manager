<?php

use BitApps\FM\Http\Controllers\FileManagerController;
use BitApps\WPKit\Http\Router\Route;

if (!\defined('ABSPATH')) {
    exit;
}

Route::match(['get', 'post'], 'connector', [FileManagerController::class, 'connector'])
    ->middleware('nonce:admin');

Route::noAuth()
    ->match(['get', 'post'], 'connector_front', [FileManagerController::class, 'connector'])
    ->middleware('nonce:public');
