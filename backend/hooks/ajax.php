<?php

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Router\Route;
use BitApps\FM\Http\Controllers\FileManagerController;
use BitApps\FM\Http\Controllers\LogController;
use BitApps\FM\Http\Controllers\SettingsController;

if (!\defined('ABSPATH')) {
    exit;
}

Route::group(
    function () {
        Route::match(['get', 'post'], 'connector', [FileManagerController::class, 'connector']);
        Route::noAuth()->match(['get', 'post'], 'connector_front', [FileManagerController::class, 'connector']);
        Route::post('theme', [FileManagerController::class, 'changeThemes']);

        Route::post('logs/all', [LogController::class, 'all']);

        Route::get('settings/get', [SettingsController::class, 'get']);
    }
)->middleware('nonce');
