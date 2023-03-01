<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use WP_User;

\defined('ABSPATH') or exit();
class PermissionsProvider
{
    public $permissions;

    public $users;

    public $roles;

    /**
     * FileManager Instance
     *
     * @var FileManager
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

        $this->fileManager    = $FileManager;
        $this->permissions    = Config::getOption(
            'permissions',
            $this->defaultPermissions()
        );
        $this->roles = array_keys($wp_roles->roles);
        $this->users = get_users(['fields' => ['ID', 'user_login', 'display_name']]);
    }

    public static function defaultPermissions()
    {
        $permissions['do_not_use_for_admin']     = 'do_not_use_for_admin';
        $permissions['file_type']                = ['text', 'image', 'application', 'video', 'audio'];
        $permissions['file_size']                = 2;
        $permissions['folder_options']           = 'common'; // common | role | user
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
            'path'     => '',
        ];

        if (
            isset($this->permissions[$type])
            && \is_array($this->permissions[$type])
            && isset($this->permissions[$type][$name])
            && \is_array($this->permissions[$type][$name])
        ) {
            $settings['path'] = isset($this->permissions[$type][$name]['path'])
                ? $this->permissions[$type][$name]['path'] : $settings['path'];
            $settings['commands'] = isset($this->permissions[$type][$name]['commands'])
                && \is_array($this->permissions[$type][$name]['commands'])
                ? $this->permissions[$type][$name]['commands'] : $settings['commands'];
        }

        return $settings;
    }

    public function getGuestSettings()
    {
        $settings = [
            'commands' => [],
            'path'     => '',
        ];

        if (
            isset($this->permissions['guest'])
            && \is_array($this->permissions['guest'])
        ) {
            $settings['path'] = isset($this->permissions['guest']['path'])
                ? $this->permissions['guest']['path'] : $settings['path'];
            $settings['commands'] = isset($this->permissions['guest']['commands'])
                && \is_array($this->permissions['guest']['commands'])
                ? $this->permissions['guest']['commands'] : $settings['commands'];
        }

        return $settings;
    }

    public function getFolderOption()
    {
        return isset($this->permissions['folder_options'])
            ? $this->permissions['folder_options'] : 'common';
    }

    public function getEnabledFileType()
    {
        return isset($this->permissions['file_type'])
            ? $this->permissions['file_type'] : [];
    }

    public function getMaximumUploadSize()
    {
        return isset($this->permissions['file_size'])
            ? $this->permissions['file_size'] : 2;
    }

    public function getPublicRootPath()
    {
        return isset($this->permissions['root_folder'])
            ? stripslashes($this->permissions['root_folder'])
            : $this->getDefaultPublicRootPath() . DIRECTORY_SEPARATOR;
    }

    public function getPublicRootURL()
    {
        return isset($this->permissions['root_folder_url'])
            ? stripslashes($this->permissions['root_folder_url'])
            : $this->getDefaultPublicRootURL();
    }

    public function isEnabledForAdmin()
    {
        return isset($this->permissions['do_not_use_for_admin'])
            && $this->permissions['do_not_use_for_admin'] === 'do_not_use_for_admin';
    }

    public function isCommonFolderEnabled()
    {
        return isset($this->permissions['do_not_use_for_admin'])
            && $this->permissions['do_not_use_for_admin'] === 'do_not_use_for_admin';
    }

    public function currentUser()
    {
        if (!isset($this->currentUser) && \function_exists('wp_get_current_user')) {
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
