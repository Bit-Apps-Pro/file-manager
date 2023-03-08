<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Plugin;

\defined('ABSPATH') or exit();

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
        if (strpos(basename($path), '.') !== 0 || \strlen($relPath) === 1 || $attr === 'locked') {
            return;
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
}
