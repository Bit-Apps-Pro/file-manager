<?php

namespace BitApps\FM\Http\Requests;

use BitApps\WPKit\Http\Request\Request;

class FileManagerRequest extends Request
{
    public function rules()
    {
        return [
            'action' => ['required','string'],
            'theme'  => ['required','string']
        ];
    }
}
