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

    public function control($attr, $path, $data, $volume)
    {
        if (!$this->settings->getVisibilityOfHiddenFile()) {
            $readWrite          = $attr == 'read' || $attr == 'write';
            $isReadWriteAllowed = $this->settings->isHiddenFolderAllowed()
            ? $readWrite : ! $readWrite;

            return strpos(basename($path), '.') === 0
                ? $isReadWriteAllowed : null;
        }
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
        if ($this->settings->isHiddenFolderAllowed()) {
            return true;
        }

        return strpos($name, '.') !== 0;
    }
}
