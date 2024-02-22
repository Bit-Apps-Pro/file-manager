<?php

namespace BitApps\FM\Http\Requests;

use BitApps\FM\Dependencies\BitApps\WPKit\Http\Request\Request;
use BitApps\FM\Dependencies\BitApps\WPKit\Utils\Capabilities;
use BitApps\FM\Http\Rules\ValidPathRule;

class SettingsUpdateRequest extends Request
{
    public function authorize()
    {
        return Capabilities::filter('can_change_fm_settings', 'install_plugins');
    }

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
            'theme'                       => ['required','string', 'sanitize:text'],
            'default_view_type'           => ['required','string', 'sanitize:text'],
            'root_folder_path'            => ['required','string', 'sanitize:text', ValidPathRule::class],
            'root_folder_url'             => ['required','string', 'sanitize:text', 'url'],
            'size.width'                  => ['required','string', 'sanitize:text'],
            'size.height'                 => ['required','sanitize:text'],
            'display_ui_options'          => ['required','string'],
        ];
    }
}
