<?php

namespace BitApps\FM\Http\Requests\Permissions;

use BitApps\FM\Http\Rules\ValidateCommandsRule;
use BitApps\FM\Http\Rules\ValidPathRule;
use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;

/**
 * @property int    $page
 * @property string $path
 * @property array  $commands
 */
class AddUserPermissionRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm_can_change_permissions', 'install_plugins');
    }

    public function rules()
    {
        return [
            'id'                   => ['sanitize:text', 'required','Integer'],
            'path'                 => ['nullable', ValidPathRule::class],
            'commands'             => ['nullable', ValidateCommandsRule::class]
        ];
    }
}
