<?php

use BitApps\FM\Http\Controllers\FileManagerController;
use BitApps\FM\Http\Controllers\LogController;
use BitApps\FM\Http\Controllers\PermissionsController;
use BitApps\FM\Http\Controllers\SettingsController;
use BitApps\FM\Http\Controllers\TelemetryPopupController;
use BitApps\WPKit\Http\Router\Route;

if (!\defined('ABSPATH')) {
    exit;
}

Route::group(
    function () {
        Route::match(['get', 'post'], 'connector', [FileManagerController::class, 'connector']);

        Route::post('theme/update', [SettingsController::class, 'updateTheme']);

        Route::get('language/get', [SettingsController::class, 'getLanguages']);
        Route::post('language/update', [SettingsController::class, 'updateLanguage']);

        Route::post('logs/all', [LogController::class, 'all'])->middleware('cap:bitapps_fm_can_access_logs');

        Route::get('settings/get', [SettingsController::class, 'get']);
        Route::post('settings/update', [SettingsController::class, 'update']);
        Route::post('settings/toggle-view', [SettingsController::class, 'toggleView']);

        Route::get('permissions/get', [PermissionsController::class, 'get']);
        Route::post('permissions/update', [PermissionsController::class, 'update']);

        Route::post('telemetry_permission_handle', [TelemetryPopupController::class, 'handleTelemetryPermission']);
        Route::get('telemetry_popup_disable_check', [TelemetryPopupController::class, 'isPopupDisabled']);
    }
)->middleware('nonce:admin');

Route::noAuth()
    ->match(['get', 'post'], 'connector_front', [FileManagerController::class, 'connector'])
    ->middleware('nonce:public');
