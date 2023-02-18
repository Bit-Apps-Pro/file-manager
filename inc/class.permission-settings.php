<?php

defined('ABSPATH') or die();



class BFMFileManagerPermissionSettings
{

    public $settings;

    public $users;

    public $roles;

    public function __construct()
    {
        global $wp_roles;
        $this->settings = get_option(
            'file_manager_permissions',
            $this->defaultPermissions()
        );
        $this->roles = array_keys($wp_roles->roles);
        $this->users = get_users(['fields' => ['ID', 'user_login', 'display_name']]);
    }

    public static function defaultPermissions()
    {
        $permissions['do-not-use-for-admin'] = 'do-not-use-for-admin';
        $permissions['file-type'] = ['text', 'image', 'application', 'video', 'audio'];
        $permissions['file-size'] = 2;
        $permissions['single-folder'] = 'folder_options_single';
        $permissions['folder_options-separate'] = 'separate-folder';
        $permissions['folder_options-userrole'] = 'userrole-folder';
        $permissions['by_role']['administrator'] = [
            'commands' => [
                'download', 'upload', 'cut', 'copy', 'duplicate',
                'paste', 'rm', 'mkdir', 'mkfile', 'edit', 'rename',
                'archive', 'extract'
            ],
            'path' => ''
        ];

        return $permissions;
    }

    public function getByRole($role)
    {
    }

    public function getByUser($user)
    {
    }


    public function getEnabledFileType()
    {
        return isset($this->settings['file-type'])
            ? $this->settings['file-type'] : [];
    }

    public function getMaximumUploadSize()
    {
        return isset($this->settings['file-size'])
            ? $this->settings['file-size'] : 2;
    }

    public function isEnabledForAdmin()
    {
        return isset($this->settings['do-not-use-for-admin'])
            && $this->settings['do-not-use-for-admin'] === 'do-not-use-for-admin';
    }
}
