<?php

namespace BitApps\FM\Http\Requests\Settings;

use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;
use BitApps\FM\Http\Rules\ValidateThemeRule;

class ThemeUpdateRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm__can_change_theme');
    }

    public function rules()
    {
        return [
            'theme'  => ['required','string', ValidateThemeRule::class],
        ];
    }
}
