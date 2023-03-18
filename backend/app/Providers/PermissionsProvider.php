<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Core\Utils\Capabilities;
use BitApps\FM\Exception\PreCommandException;
use BitApps\FM\Plugin;
use elFinder;
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
     * @var WP_User
     */
    public $currentUser;

    /**
     * Dashboard preferences
     *
     * @var PreferenceProvider
     */
    private $_preferences;

    public function __construct()
    {
        global $wp_roles;
        $this->permissions    = Config::getOption(
            'permissions',
            $this->defaultPermissions()
        );

        $this->_preferences = Plugin::instance()->preferences();
        $this->roles       = array_keys($wp_roles->roles);
        $this->users       = get_users(['fields' => ['ID', 'user_login', 'display_name']]);
    }

    public function allRoles()
    {
        return $this->roles;
    }

    /**
     * Returns all available users
     *
     * @return array<int, WP_User>
     */
    public function allUsers()
    {
        return $this->users;
    }

    public function allCommands()
    {
        return [
            'download', // file, zipdl
            'cut',// only for frontend. send cmd as paste
            'copy',// only for frontend. send cmd as paste
            'edit', // put
            'rm', // rm
            'upload',// upload
            'duplicate', // duplicate
            'paste', // paste
            'mkfile',// mkfile
            'mkdir',// mkdir
            'rename', // rename
            'archive', // archive
            'extract',// extract
        ];
    }

    public function defaultPermissions()
    {
        $permissions['do_not_use_for_admin']     = 'do_not_use_for_admin';
        $permissions['file_type']                = ['text', 'image', 'application', 'video', 'audio'];
        $permissions['file_size']                = 2;
        $permissions['folder_options']           = 'common'; // common | role | user
        $permissions['by_role']['administrator'] = [
            'commands' => $this->allCommands(),
            'path'     => FM_UPLOAD_BASE_DIR,
        ];

        return $permissions;
    }

    public function getPath()
    {
        if (is_user_logged_in() && is_admin() && $this->isDisabledForAdmin()) {
            return $this->_preferences->getRootPath();
        }

        $path = '';

        if (!is_user_logged_in()) {
            $path = $this->getGuestPermissions()['path'];
        } elseif ($this->isCurrentUserHasPermission()) {
            $path = $this->permissionsForCurrentUser()['path'];
        } else {
            $path = $this->permissionsForCurrentRole()['path'];
        }

        if (empty($path) || !file_exists($path)) {
            throw new PreCommandException(__('please check root folder for file manager, from file manager settings', 'file-manager'));
        }

        return $path;
    }

    public function getURL()
    {
        if (is_user_logged_in() && is_admin() && $this->isDisabledForAdmin()) {
            return $this->_preferences->getRootUrl();
        }

        return home_url() . "/" . str_replace([ABSPATH, '\\'], ['', '/'], $this->getPath());
    }

    public function getVolumeAlias()
    {
        return $this->_preferences->getRootVolumeName();
    }

    public function getDefaultPublicRootPath()
    {
        return FM_UPLOAD_BASE_DIR;
    }

    public function getDefaultPublicRootURL()
    {
        return FM_UPLOAD_BASE_URL;
    }

    public function getPublicRootPath()
    {
        return isset($this->permissions['root_folder'])
            ? stripslashes($this->permissions['root_folder'])
            : $this->getDefaultPublicRootPath();
    }

    public function getPublicRootURL()
    {
        return isset($this->permissions['root_folder_url'])
            ? stripslashes($this->permissions['root_folder_url'])
            : $this->getDefaultPublicRootURL();
    }

    public function getDefaultRootPathByCriteria($criteria, $type)
    {
        $defaultPath = $this->getDefaultPublicRootPath();
        $rootPath    = wp_unslash($defaultPath) . DIRECTORY_SEPARATOR . "{$type}_{$criteria}";
        if (!file_exists($rootPath) && is_dir($defaultPath) && is_writable($defaultPath)) {
            wp_mkdir_p($rootPath);
        }

        if (!file_exists($rootPath) || !is_dir($rootPath) || !is_readable($rootPath)) {
            $rootPath = '';
        }

        return $rootPath;
    }

    public function getDefaultRootPathForUser($userID)
    {
        return $this->getDefaultRootPathByCriteria($userID, 'user');
    }

    public function getDefaultRootPathForRole($role)
    {
        return $this->getDefaultRootPathByCriteria($role, 'role');
    }

    public function getByRole($role)
    {
        return $this->getPermissions('by_role', $role);
    }

    public function getByUser($userID)
    {
        return $this->getPermissions('by_user', $userID);
    }

    public function getPermissions($type, $name)
    {
        if ($this->isCommonFolderEnabled()) {
            $defaultPath = $this->getDefaultPublicRootPath();
        } elseif ($type === 'by_user') {
            $defaultPath = $this->getDefaultRootPathForUser($name);
        } else {
            $defaultPath = $this->getDefaultRootPathForRole($name);
        }

        $settings = [
            'commands' => [],
            'path'     => $defaultPath,
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

    public function getGuestPermissions()
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

    public function isDisabledForAdmin()
    {
        return isset($this->permissions['do_not_use_for_admin'])
            && $this->permissions['do_not_use_for_admin'] === 'do_not_use_for_admin';
    }

    public function getFolderOption()
    {
        return isset($this->permissions['folder_options']) ? $this->permissions['folder_options'] : 'common';
    }

    public function isCommonFolderEnabled()
    {
        return isset($this->permissions['folder_options']) && $this->permissions['folder_options'] === 'common';
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
        if (!is_user_logged_in() || !$this->currentUser() instanceof WP_User) {
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
        $hasPermission = true;

        if (empty($this->permissionsForCurrentUser()['commands'])) {
            $hasPermission = false;
        }

        return $hasPermission;
    }

    public function isCurrentRoleHasPermission()
    {
        $hasPermission = true;

        if (empty($this->permissionsForCurrentRole()['commands'])) {
            $hasPermission = false;
        }

        return $hasPermission;
    }

    public function permissionsForCurrentUser()
    {
        return $this->getByUser($this->currentUserID());
    }

    public function permissionsForCurrentRole()
    {
        return $this->getByRole($this->currentUserRole());
    }

    public function currentUserCanRun($command)
    {
        if (is_user_logged_in() && is_admin() && $this->isDisabledForAdmin()) {
            return true;
        }

        $permission = false;
        if (
            \in_array($command, $this->permissionsForCurrentUser()['commands'])
        || \in_array($command, $this->permissionsForCurrentRole()['commands'])
        ) {
            $permission = true;
        }

        if (!is_user_logged_in() && \in_array($command, $this->getGuestPermissions()['commands'])) {
            $permission = true;
        }

        $cap = Config::VAR_PREFIX . 'user_can_' . $command;

        return Capabilities::filter($cap) || $permission;
    }

    public function getEnabledCommand()
    {
        if (is_user_logged_in() && is_admin() && $this->isDisabledForAdmin()) {
            return ['*'];
        }

        if (!is_user_logged_in()) {
            $enabledCommands = $this->getGuestPermissions()['commands'];
        } elseif ($this->isCurrentUserHasPermission()) {
            $enabledCommands = $this->permissionsForCurrentUser()['commands'];
        } else {
            $enabledCommands = $this->permissionsForCurrentRole()['commands'];
        }
        
        return $enabledCommands;
    }
    public function getDisabledCommand()
    {
        if (is_user_logged_in() && is_admin() && $this->isDisabledForAdmin()) {
            return [];
        }

        $enabledCommands = $this->getEnabledCommand();

        $disabledCommand = [];
        foreach ($this->allCommands() as $command) {
            if (!\in_array($command, $enabledCommands)) {
                $disabledCommand[] = $command;
            }
        }

        return $disabledCommand;
    }
}
