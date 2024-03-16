<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Exception\PreCommandException;
use BitApps\FM\Plugin;

\defined('ABSPATH') || exit();

class AccessControlProvider
{
    public $settings;

    public function __construct()
    {
        $this->settings = Plugin::instance()->preferences();
    }

    /**
     * Handles hidden file/folder access
     *
     * @param string    $attr    attribute name (read|write|locked|hidden)
     * @param string    $path    absolute file path
     * @param string    $data    value of volume option `accessControlData`
     * @param object    $volume  elFinder volume driver object
     * @param bool|null $isDir   path is directory (true: directory, false: file, null: unknown)
     * @param string    $relPath file path relative to volume root directory started with directory separator
     *
     * @return bool|null
     */
    public function control($attr, $path, $data, $volume, $isDir, $relPath)
    {
        $isAccessAllowed = null;
        if (strpos(basename($path), '.') !== 0 || \strlen($relPath) === 1 || $attr === 'locked') {
            return $isAccessAllowed;
        }

        $isAccessAllowed = true;

        if ($this->settings->getVisibilityOfHiddenFile() && $attr === 'hidden') {
            $isAccessAllowed = false;
        }

        if ($isAccessAllowed && !$this->settings->isHiddenFolderAllowed() && $attr == 'write') {
            $isAccessAllowed = false;
        }

        return $isAccessAllowed;
    }

    /**
     * Create or upload .( Dot) started files or folder based on settings.
     * reference : https://github.com/Studio-42/elFinder/wiki/Connector-configuration-options-2.1#acceptedName
     *
     * @param $name file.
     *
     * @return bool
     */
    public function validateName($name)
    {
        return ! (strpos($name, '.') === 0 && !$this->settings->isHiddenFolderAllowed());
    }

    public function checkPermission($command, ...$args)
    {
        if (\in_array($command, ['open'])) {
            return;
        }

        $error              = '';
        $permissionProvider = Plugin::instance()->permissions();
        $cmd                = $command;
        if ($command === 'file' || $command === 'zipdl') {
            $cmd = 'download';
        } elseif ($command === 'put') {
            $cmd = 'edit';
        }

        if (
            $this->isNotRequiredCommandForAllPermission($cmd, $permissionProvider)
             && !$permissionProvider->currentUserCanRun($cmd)) {
            $error = wp_sprintf(
                // translators: 1: elFInder Command
                __(
                    'You are not authorized to run this command [ %s ] on file manager',
                    'file-manager'
                ),
                $cmd
            );
        }

        if (!empty($error)) {
            try {
                throw new PreCommandException($error);
            } catch (PreCommandException $th) {
                return $th->getError();
            }
        }
    }

    /**
     * If a user has permission for any task to perform then they have
     * to be allowed to perform common commands.
     * Lets say, A user has permission to upload file then they will not be
     * able to upload if ls command is not allowed.
     * Checks if the command is required for all commands to perform.
     * Common commands are:
     * - ls
     * - tree
     * - search
     * - info
     * - size
     *
     * @param string              $cmd
     * @param PermissionsProvider $permissionProvider
     *
     * @return bool
     */
    public function isNotRequiredCommandForAllPermission($cmd, $permissionProvider)
    {
        $isNotRequired = true;

        if (
            \in_array($cmd, ['ls', 'tree', 'info', 'size'])
            && (
                $permissionProvider->isCurrentRoleHasPermission()
                || $permissionProvider->isCurrentUserHasPermission()
            )
        ) {
            $isNotRequired = false;
        }

        return $isNotRequired;
    }
}
