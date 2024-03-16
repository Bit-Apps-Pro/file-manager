<?php

namespace BitApps\FM\Http\Requests\Settings;

use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;

class SettingsRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm_can_view_settings', 'install_plugins');
    }
}
