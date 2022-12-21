<?php
/***
 * If try to direct access  plugin folder it will Exit
 **/
if (! defined('ABSPATH')) {
    exit;
}

use BitCode\FI\Core\Http\Router\Route;
use BitCode\FI\Flow\Flow;
use BitCode\FI\Log\LogHandler;
use BitCode\FI\Triggers\TriggerController;

Route::post('log/get', [LogHandler::class, 'get']);
Route::post('log/delete', [LogHandler::class, 'delete']);

Route::no_auth()->ignore_token()->post('trigger/list', [TriggerController::class, 'triggers']);

Route::get('flow/list', [Flow::class, 'list']);
Route::post('flow/get', [Flow::class, 'get']);
Route::post('flow/save', [Flow::class, 'save']);
Route::post('flow/update', [Flow::class, 'update']);
Route::post('flow/delete', [Flow::class, 'delete']);
Route::post('flow/bluk-delete', [Flow::class, 'bulkDelete']);
Route::post('flow/toggleStatus', [Flow::class, 'toggle_status']);
