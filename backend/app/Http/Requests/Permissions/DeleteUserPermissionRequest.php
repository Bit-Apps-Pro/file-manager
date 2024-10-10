<?php

namespace BitApps\FM\Http\Requests\Permissions;

use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;

class DeleteUserPermissionRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm_can_change_permissions', 'install_plugins');
    }

    public function rules()
    {
        return [
            'id'                   => ['sanitize:text', 'required','Integer']
        ];
    }
}
