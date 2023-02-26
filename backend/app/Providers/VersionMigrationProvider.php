<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;

\defined('ABSPATH') or exit();

class VersionMigrationProvider
{
    private $_oldVersion;

    private $_currentVersion;

    public function __construct()
    {
        $this->_oldVersion     = Config::getOption('version', Config::VERSION_ID - 1);
        $this->_currentVersion = Config::VERSION_ID;
    }

    public function migrate()
    {
        $function = 'migrateTo' . $this->_oldVersion;
        if ($this->_oldVersion < $this->_currentVersion
        && method_exists($this, $function)
        ) {
            $this->{$function}();
        }
    }

    private function migrateTo600()
    {
        delete_option('fm_current_version');
        $this->renameSettingsOptionV600();
        $this->migrateTo502();
    }

    private function migrateTo502()
    {
        $logFile = FM_UPLOAD_BASE_DIR . DIRECTORY_SEPARATOR . 'log.txt';
        if (file_exists($logFile)) {
            unlink($logFile);
        }
    }

    private function renameSettingsOptionV600()
    {
        $previousSettings = get_option('file-manager', false);
        if ($previousSettings) {
            Config::addOption(Config::withPrefix('settings'), $previousSettings, true);
            delete_option('file-manager');
        }
    }
}
