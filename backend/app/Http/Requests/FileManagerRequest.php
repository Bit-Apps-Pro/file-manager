<?php

namespace BitApps\FM\Http\Requests;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;

class FileManagerRequest extends Request
{
    public function rules()
    {
        return [
            'action' => ['required','string'],
            'theme'  => ['required','string'],
        ];
    }
}
