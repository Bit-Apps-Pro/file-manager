<?php

namespace BitApps\FM\Providers;

use BitApps\FM\Config;
use BitApps\FM\Core\Database\Connection;
use BitApps\FM\Core\Installer;

class InstallerProvider
{
    private $_activateHook;

    private static $_uninstallHook;

    public function __construct()
    {
        Connection::setPluginDBPrefix(Config::DB_PREFIX);
        $this->_activateHook   = Config::withPrefix('activate');
        self::$_uninstallHook  = Config::withPrefix('uninstall');
    }

    public function register()
    {
        $installer = new Installer(
            [
                'php'        => Config::REQUIRED_PHP_VERSION,
                'wp'         => Config::REQUIRED_WP_VERSION,
                'version'    => Config::VERSION,
                'oldVersion' => Config::getOption('version', '0.0'),
                'multisite'  => false,
                'basename'   => Config::get('BASENAME'),
            ],
            [
                'activate'  => $this->_activateHook,
                'uninstall' => self::$_uninstallHook,
            ],
            [

                'migration' => $this->migration(),
                'drop'      => $this->drop(),
            ]
        );
        $installer->register();
    }

    public static function migration()
    {
        $migrations = [
            'BFMLogsTableMigration',
            'BFMPluginOptions',
        ];

        return [
            'path'       => Config::get('BASEDIR')
                . DIRECTORY_SEPARATOR
                . 'db'
                . DIRECTORY_SEPARATOR
                . 'Migrations'
                . DIRECTORY_SEPARATOR,
            'migrations' => $migrations,
        ];
    }

    public static function drop()
    {
        $migrations = [
            'BFMLogTableMigration',
            'BFMPluginOptions',
        ];

        return [
            'path'       => Config::get('BASEDIR')
                . DIRECTORY_SEPARATOR
                . 'db'
                . DIRECTORY_SEPARATOR
                . 'Migrations'
                . DIRECTORY_SEPARATOR,
            'migrations' => $migrations,
        ];
    }
}
