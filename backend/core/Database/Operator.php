<?php
/**
 * Class For Database Migration
 *
 * @category Database
 *
 * @author Bit Code Developer <developer@bitcode.pro>
 */

namespace BitApps\FM\Core\Database;

if (! defined('ABSPATH')) {
    exit;
}
use BitApps\FM\Config;
use FilesystemIterator;

require_once ABSPATH.'wp-admin/includes/upgrade.php';

/**
 * Database Migration
 */
final class Operator
{
    /**
     * Migrate DB tables
     *
     * @return void
     */
    public static function migrate()
    {
        foreach (self::getMigrations() as  $migration) {
            if (method_exists($migration, 'up')) {
                $migration->up();
            }
        }
        update_site_option(
            Config::withPrefix('db_version'),
            Config::DB_VERSION
        );
    }

    /**
     * DROP DB tables
     *
     * @return void
     */
    public static function drop()
    {
        foreach (self::getMigrations() as  $migration) {
            if (method_exists($migration, 'drop')) {
                $migration->drop();
            }
        }
    }

    /**
     * Migration Iterator
     *
     * @return array[Migration]
     */
    public static function getMigrations()
    {
        $migrationsPath = Config::get('BASEDIR')
        .DIRECTORY_SEPARATOR
        .'db'
        .DIRECTORY_SEPARATOR
        .'Migrations'
        .DIRECTORY_SEPARATOR;

        $migrations = new FilesystemIterator(
            $migrationsPath,
            FilesystemIterator::SKIP_DOTS
        );
        $migration_classes = [];
        foreach ($migrations as $migration) {
            if ($migration->isFile()) {
                $migrationName = str_replace(
                    ' ',
                    '',
                    ucwords(
                        str_replace(
                            ['-', '_', '.php'],
                            ' ',
                            preg_replace(
                                '/[0-9]/',
                                '',
                                $migration->getBasename()
                            )
                        )
                    )
                );
                if (! class_exists($migrationName)) {
                    include $migration->getPathname();
                    $migration_classes[intval(preg_split('/_/', $migration->getBasename())[0])] = new $migrationName();
                }
            }
        }
        ksort($migration_classes);

        return $migration_classes;

    }
}
