<?php

namespace BitApps\FM\Providers;

\defined('ABSPATH') or exit();

class AccessControlProvider
{
    public $settings;

    public function __construct()
    {
        global $FileManager;
        $this->settings = $FileManager->options['file_manager_settings'];
    }

    public function control($attr, $path, $data, $volume)
    {
        if (!isset($this->settings['fm-show-hidden-files']) || empty($this->settings['fm-show-hidden-files'])) {
            return strpos(basename($path), '.') === 0
                ? (isset($this->settings['fm-create-hidden-files-folders']) && '' == $this->settings['fm-create-hidden-files-folders']) ? !($attr == 'read' || $attr == 'write') : ($attr == 'read' || $attr == 'write')
                : null;
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
        if (isset($this->settings['fm-create-hidden-files-folders']) && 'fm-create-hidden-files-folders' == $this->settings['fm-create-hidden-files-folders']) {
            return true;
        }

        return strpos($name, '.') !== 0;
    }
}
