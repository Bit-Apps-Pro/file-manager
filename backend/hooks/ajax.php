<?php

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Router\Route;
use BitApps\FM\Http\Controllers\FileManagerController;
use BitApps\FM\Http\Controllers\LogController;
use BitApps\FM\Http\Controllers\PermissionsController;
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
        Route::post('settings/update', [SettingsController::class, 'update']);

        Route::get('permissions/get', [PermissionsController::class, 'get']);
        Route::post('permissions/update', [PermissionsController::class, 'update']);
    }
)->middleware('nonce:admin');
