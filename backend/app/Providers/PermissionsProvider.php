<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Exception\PreCommandException;
use BitApps\FM\Plugin;
use BitApps\WPKit\Utils\Capabilities;
use WP_User;

\defined('ABSPATH') || exit();
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
        $this->permissions = Config::getOption(
            'permissions',
            $this->defaultPermissions()
        );

        if (\array_key_exists('do_not_use_for_admin', $this->permissions)) {
            $this->permissions['do_not_use_for_admin'] = \boolval($this->permissions['do_not_use_for_admin']);
        }

        $this->_preferences = Plugin::instance()->preferences();
        $this->roles        = array_keys($wp_roles->roles);
        $this->users        = $this->mappedUsers();
    }

    public function refresh()
    {
        $this->permissions    = Config::getOption(
            'permissions',
            $this->defaultPermissions()
        );
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

    /**
     * Get user display name by id
     *
     * @param mixed $id
     *
     * @return string
     */
    public function getUserDisplayName($id)
    {
        return isset($this->users[$id]) ? $this->users[$id]->display_name : 'guest';
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
        $permissions['do_not_use_for_admin']     = true;
        $permissions['fileType']                = apply_filters(
            Config::withPrefix('filter_file_type'),
            []
        );
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
        if ($this->isRequestForAdminArea() && $this->isDisabledForAdmin()) {
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

        if (empty($path) || !is_readable($path)) {
            throw new PreCommandException(esc_html__('please check root folder for file manager, from file manager settings', 'file-manager'));
        }

        return $path;
    }

    public function getURL()
    {
        if ($this->isRequestForAdminArea() && $this->isDisabledForAdmin()) {
            return $this->_preferences->getRootUrl();
        }

        return home_url() . '/' . str_replace([ABSPATH, '\\'], ['', '/'], $this->getPath());
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

    public function getPublicRootPathByCriteria($criteria, $type)
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

    public function getPublicRootPathForUser($userID)
    {
        return $this->getPublicRootPathByCriteria($userID, 'user');
    }

    public function getPublicRootPathForRole($role)
    {
        return $this->getPublicRootPathByCriteria($role, 'role');
    }

    public function getPathByFolderOption()
    {
        switch ($this->getFolderOption()) {
            case 'role':
                return $this->getPublicRootPathForRole($this->currentUserRole());
            case 'user':
                return $this->getPublicRootPathForRole($this->currentUserRole());
            default:
                return $this->getPublicRootPath();
        }
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

            // commands in guest permission removed. this is for back compat
            if (
                isset($this->permissions['guest']['commands'])
            && \is_array($this->permissions['guest']['commands'])
            ) {
                $settings['commands'] =  $this->permissions['guest']['commands'];
            } elseif (
                \array_key_exists('can_download', $this->permissions['guest'])
                 && $this->permissions['guest']['can_download']
            ) {
                $settings['commands'] = ['download'];
            }
        }

        return $settings;
    }

    public function getEnabledFileType()
    {
        return isset($this->permissions['fileType'])
            ? $this->permissions['fileType'] : [];
    }

    public function getMaximumUploadSize()
    {
        return isset($this->permissions['file_size'])
            ? $this->permissions['file_size'] : 2;
    }

    public function isDisabledForAdmin()
    {
        return isset($this->permissions['do_not_use_for_admin'])
            && \boolval($this->permissions['do_not_use_for_admin']);
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
        if (Capabilities::check('administrator') && $this->isDisabledForAdmin()) {
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
        if ($this->isRequestForAdminArea() && $this->isDisabledForAdmin()) {
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
        if ($this->isRequestForAdminArea() && $this->isDisabledForAdmin()) {
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

    public function updatePermissionSetting($permissions)
    {
        return Config::updateOption('permissions', $permissions, 'yes');
    }

    public function isRequestForAdminArea()
    {
        $action = '';

        if (isset($_REQUEST['action'])) {
            $action = sanitize_key($_REQUEST['action']);
        }

        return is_user_logged_in() && $action === 'bit_fm_connector';
    }

    public function isRequestForShortcode()
    {
        $action = '';

        if (isset($_REQUEST['action'])) {
            $action = sanitize_key($_REQUEST['action']);
        }

        return $action === Config::withPrefix('connector_front');
    }

    /**
     * Returns all available users. Array Index will be user ID
     *
     * @return array<int, WP_User>
     */
    private function mappedUsers()
    {
        $users          = get_users(['fields' => ['ID', 'user_login', 'display_name']]);
        $processedUsers = [];

        foreach ($users as $user) {
            $processedUsers[$user->ID] = $user;
        }

        return $processedUsers;
    }
}
