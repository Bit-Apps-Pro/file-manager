<?php

defined('ABSPATH') or die();



class BFMFileManagerPermissionSettings
{

    public $settings;

    public $users;

    public $roles;

    /**
     * FileManager Instance
     *
     * @var FM
     */
    public $fileManager;

    /**
     * FileManager Instance
     *
     * @var WP_User
     */
    public $currentUser;

    public function __construct()
    {
        global $wp_roles, $FileManager;

        $this->fileManager = $FileManager;
        $this->settings = get_option(
            'file_manager_permissions',
            $this->defaultPermissions()
        );
        $this->roles = array_keys($wp_roles->roles);
        $this->users = get_users(['fields' => ['ID', 'user_login', 'display_name']]);
    }

    public static function defaultPermissions()
    {
        $permissions['do_not_use_for_admin'] = 'do_not_use_for_admin';
        $permissions['file_type'] = ['text', 'image', 'application', 'video', 'audio'];
        $permissions['file_size'] = 2;
        $permissions['folder_options'] = 'common'; // common | role | user
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

    public function getDefaultPublicRootPath()
    {
        return FM_UPLOAD_BASE_DIR . DIRECTORY_SEPARATOR;
    }

    public function getDefaultPublicRootURL()
    {
        return FM_UPLOAD_BASE_URL;
    }


    public function getByRole($role)
    {
        return $this->getSettings('by_role', $role);
    }

    public function getByUser($user)
    {
        return $this->getSettings('by_user', $user);
    }

    public function getSettings($type, $name)
    {
        $settings = [
            'commands' => [],
            'path' => '',
        ];

        if (
            isset($this->settings[$type])
            && is_array($this->settings[$type])
            && isset($this->settings[$type][$name])
            && is_array($this->settings[$type][$name])
        ) {
            $settings['path'] = isset($this->settings[$type][$name]['path']) ?
                $this->settings[$type][$name]['path'] : $settings['path'];
            $settings['commands'] = isset($this->settings[$type][$name]['commands'])
                && is_array($this->settings[$type][$name]['commands']) ?
                $this->settings[$type][$name]['commands'] : $settings['commands'];
        }
        return $settings;
    }

    public function getGuestSettings()
    {
        $settings = [
            'commands' => [],
            'path' => '',
        ];

        if (
            isset($this->settings['guest'])
            && is_array($this->settings['guest'])
        ) {
            $settings['path'] = isset($this->settings['guest']['path']) ?
                $this->settings['guest']['path'] : $settings['path'];
            $settings['commands'] = isset($this->settings['guest']['commands'])
                && is_array($this->settings['guest']['commands']) ?
                $this->settings['guest']['commands'] : $settings['commands'];
        }
        return $settings;
    }


    public function getFolderOption()
    {
        return isset($this->settings['folder_options'])
            ? $this->settings['folder_options'] : 'common';
    }

    public function getEnabledFileType()
    {
        return isset($this->settings['file_type'])
            ? $this->settings['file_type'] : [];
    }

    public function getMaximumUploadSize()
    {
        return isset($this->settings['file_size'])
            ? $this->settings['file_size'] : 2;
    }

    public function getPublicRootPath()
    {
        return isset($this->settings['root_folder'])
            ? stripslashes($this->settings['root_folder'])
            : $this->getDefaultPublicRootPath() . DIRECTORY_SEPARATOR;
    }

    public function getPublicRootURL()
    {
        return isset($this->settings['root_folder_url'])
            ? stripslashes($this->settings['root_folder_url'])
            : $this->getDefaultPublicRootURL();
    }

    public function isEnabledForAdmin()
    {
        return isset($this->settings['do_not_use_for_admin'])
            && $this->settings['do_not_use_for_admin'] === 'do_not_use_for_admin';
    }

    public function isCommonFolderEnabled()
    {
        return isset($this->settings['do_not_use_for_admin'])
            && $this->settings['do_not_use_for_admin'] === 'do_not_use_for_admin';
    }

    public function currentUser()
    {
        if (!isset($this->currentUser) && function_exists('wp_get_current_user')) {
            $this->currentUser = wp_get_current_user();
        }
        return $this->currentUser;
    }

    public function currentUserRole()
    {
        if (!$this->currentUser() instanceof WP_User) {
            return false;
        }
        return $this->currentUser()->roles[0];
    }

    public function currentUserID()
    {
        if (!$this->currentUser() instanceof WP_User) {
            return false;
        }
        return $this->currentUser()->ID;
    }

    public function isCurrentUserHasPermission()
    {
        $hasPermission = false;

        if (!empty($this->getByUser($this->currentUserID()))
            || !empty($this->getByRole($this->currentUserRole()))
        ) {
            $hasPermission = true;
        }
        return $hasPermission;
    }
}
