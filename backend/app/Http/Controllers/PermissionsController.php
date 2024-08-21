<?php

namespace BitApps\FM\Http\Controllers;

use BitApps\WPKit\Http\Response;
use BitApps\FM\Http\Requests\Permissions\PermissionsGetRequest;
use BitApps\FM\Http\Requests\Permissions\PermissionsUpdateRequest;
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
                'users'       => array_values($this->permissionProvider->allUsers()),
                'commands'    => $this->permissionProvider->allCommands(),
                'fileTypes'   => ['text', 'image', 'application', 'video', 'audio', 'php'],
                'wpRoot'      => ABSPATH,
            ]
        );
    }

    public function update(PermissionsUpdateRequest $request)
    {
        if ($this->permissionProvider->updatePermissionSetting($request->validated())) {
            return Response::success([])->message('Permission updated successfully');
        }

        return Response::error([])->message('failed to update permission');
    }
}
