<?php
/***
 * If try to direct access  plugin folder it will Exit
 **/

use BitApps\FM\Core\Http\Router\API;
use BitApps\FM\HTTP\Controllers\FlowController;

if (! defined('ABSPATH')) {
    exit;
}


API::match(['get', 'post'], 'callback/(?P<hook_id>[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12})', [new FlowController(), 'handle'], null, ['hook_id'=> ['required'=> true, 'validate_callback' => 'wp_is_uuid']]);
