<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Vendor\BitApps\WPDatabase\Schema;
use BitApps\FM\Vendor\BitApps\WPKit\Migration\MigrationHelper;
use BitApps\FM\Vendor\BitApps\WPKit\Utils\Capabilities;
use BitApps\FM\Vendor\BitApps\WPDatabase\Connection;

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
            MigrationHelper::migrate(InstallerProvider::migration());
        }
    }

    private function migrateToLatest()
    {
        $this->migrateTo651();
    }

    private function migrateTo651()
    {
        if ($this->_oldVersion >= 651) {
            return;
        }

        Schema::withPrefix(Connection::wpPrefix() . 'fm_')->drop('logs');
        Schema::withPrefix(Connection::wpPrefix() . 'fm_')->drop('log');
        $this->migrateTo600();
    }

    private function migrateTo600()
    {
        if ($this->_oldVersion >= 600) {
            return;
        }

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
