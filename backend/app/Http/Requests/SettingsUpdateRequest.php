<?php

namespace BitApps\FM\Http\Requests;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;

class SettingsUpdateRequest extends Request
{
    public function rules()
    {
        return [
            'show_url_path'               => ['required','boolean'],
            'show_hidden_files'           => ['required','boolean'],
            'create_trash_files_folders'  => ['required','boolean'],
            'create_hidden_files_folders' => ['required','boolean'],
            'remember_last_dir'           => ['required','boolean'],
            'clear_history_on_reload'     => ['required','boolean'],
            'root_folder_name'            => ['required','string', 'sanitize:text'],
            'theme'                       => ['required','string'],
            'default_view_type'           => ['required','string'],
            'root_folder_path'            => ['required','string'],
            'root_folder_url'             => ['required','string'],
            'size.width'                  => ['required','string'],
            'size.height'                 => ['required','sanitize:text'],
            'display_ui_options'          => ['required','string'],
        ];
    }
}
