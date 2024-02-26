<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Client\Http;
use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;
use BitApps\FM\Dependencies\BitApps\WPKit\Http\Response;
use BitApps\FM\Http\Requests\Permissions\PermissionsGetRequest;
use BitApps\FM\Plugin;
use BitApps\FM\Providers\PermissionsProvider;

final class PermissionsController
{
    public PermissionsProvider $permissionProvider;

    public function __construct()
    {
        $this->permissionProvider = Plugin::instance()->permissions();
    }

    public function get(PermissionsGetRequest $request)
    {
        return Response::success(
            [
                'permissions' => $this->permissionProvider->permissions,
                'roles'       => $this->permissionProvider->allRoles(),
                'users'       => $this->permissionProvider->allUsers(),
                'commands'    => $this->permissionProvider->allCommands(),
                'fileTypes'   => ['text', 'image', 'application', 'video', 'audio'],
            ]
        );
    }

    public function update(Request $request)
    {
        if (true) {
            return Response::success([])->message('Permission updated successfully');
        }

        return Response::error([])->message('failed to update permission');
    }
}
