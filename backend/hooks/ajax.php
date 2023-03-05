<?php

use BitApps\FM\Core\Http\Router\Route;
use BitApps\FM\Providers\FileManager;

if (!\defined('ABSPATH')) {
    exit;
}
/**
 * @var FileManager $FileManager
 */
global $FileManager;
Route::match(['get', 'post'], 'connector', [$FileManager, 'connector']);
