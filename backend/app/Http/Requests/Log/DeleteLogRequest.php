<?php

namespace BitApps\FM\Http\Requests\Log;

use BitApps\WPKit\Http\Request\Request;
use BitApps\WPKit\Utils\Capabilities;

class DeleteLogRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('bitapps_fm_can_delete_log', 'manage_options');
    }

    public function rules()
    {
        return [
            'ids' => ['required','array']
        ];
    }
}
