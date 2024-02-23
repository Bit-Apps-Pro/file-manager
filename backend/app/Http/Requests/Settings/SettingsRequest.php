<?php

namespace BitApps\FM\Http\Requests\Settings;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;
use BitApps\FM\Dependencies\BitApps\WPKit\Utils\Capabilities;

class SettingsRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('can_view_fm_settings', 'install_plugins');
    }
}
