<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Core\Database\Operator as DBOperator;
use BitApps\FM\Core\Database\Schema;
use BitApps\FM\Core\Utils\Capabilities;

\defined('ABSPATH') || exit();

class VersionMigrationProvider
{
    private $_oldVersion;

    private $_currentVersion;

    public function __construct()
    {
        $this->_oldVersion     = Config::getOption('version', '528');
        $this->_currentVersion = Config::VERSION_ID;
    }

    public function migrate()
    {
        if (!Capabilities::check('manage_options')) {
            return;
        }

        if ($this->_oldVersion < $this->_currentVersion) {
            $this->migrateToLatest();
        }

        if (version_compare(Config::getOption('db_version', '0.0'), Config::DB_VERSION, '<')) {
            DBOperator::migrate(InstallerProvider::migration());
        }
    }

    private function migrateToLatest()
    {
        $this->migrateTo600();
    }

    private function migrateTo600()
    {
        if ($this->_oldVersion >= 600) {
            return;
        }

        Schema::drop('log');
        delete_option('fm_current_version');
        delete_option('fm_log');
        $this->renameSettingsOptionV600();
        $this->renameReviewOptionV600();
        $this->migrateTo502();
    }

    private function renameSettingsOptionV600()
    {
        $previousSettings = get_option('file-manager', false);
        if ($previousSettings && isset($previousSettings['file_manager_settings'])) {
            Config::addOption(
                'preferences',
                $previousSettings['file_manager_settings'],
                true
            );
            delete_option('file-manager');
        }
    }

    private function renameReviewOptionV600()
    {
        $previousReview = get_option('fm-review-data', false);
        if ($previousReview) {
            Config::addOption(
                'notify_review',
                maybe_unserialize($previousReview),
                true
            );
            delete_option('fm-review-data');
        }
    }

    private function migrateTo502()
    {
        $logFile = FM_UPLOAD_BASE_DIR . DIRECTORY_SEPARATOR . 'log.txt';
        if (file_exists($logFile)) {
            unlink($logFile);
        }
    }
}
