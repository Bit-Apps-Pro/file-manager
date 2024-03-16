<?php

namespace BitApps\FM\Http\Requests\Permissions;

use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;

class PermissionsGetRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm_can_view_settings', 'install_plugins');
    }
}
