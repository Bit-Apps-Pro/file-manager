<?php

namespace BitApps\FM\Http\Requests\Settings;

use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;
use BitApps\FM\Http\Rules\ValidateLangRule;

class LangUpdateRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm__can_change_language');
    }

    public function rules()
    {
        return [
            'lang'  => ['required','string', ValidateLangRule::class],
        ];
    }
}
