<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;
use BitApps\FM\Dependencies\BitApps\WPKit\Http\Response;

final class PermissionsController
{
    public function get()
    {
        return Response::success([]);
    }

    public function update(Request $request)
    {
        if (true) {
            return Response::success([])->message('Permission updated successfully');
        }

        return Response::error([])->message('failed to update permission');
    }
}
