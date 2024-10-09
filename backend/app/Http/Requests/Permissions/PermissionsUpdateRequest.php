<?php

namespace BitApps\FM\Http\Requests\Permissions;

use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;

/**
 * @property string $search
 * @property int    $page
 */
class SearchUserRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm_can_change_permissions', 'install_plugins');
    }

    public function rules()
    {
        return [
            'search'               => ['sanitize:text'],
            'page'                 => ['sanitize:text', 'nullable','Integer']
        ];
    }
}
