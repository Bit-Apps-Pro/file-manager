<?php

/**
 * Class For Database Migration.
 */

namespace BitApps\FM\Core\Database;

if (!\defined('ABSPATH')) {
    exit;
}

use BitApps\FM\Config;

use FilesystemIterator;

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Database Migration.
 */
final class Operator
{
    /**
     * Migrate DB tables.
     *
     * @param array $migrations Details of migration. Contains
     *                          'path' base path of migrations
     *                          'migrations' Array of Migration class
     *
     * @return void
     */
    public static function migrate($migrations)
    {
        $instance = self::getMigrationInstances($migrations);

        foreach ($instance as $migration) {
            if (method_exists($migration, 'up')) {
                $migration->up();
            }
        }
    }

    /**
     * DROP DB tables.
     *
     * @param array $migrations Details of migration. Contains
     *                          'path' base path of migrations
     *                          'migrations' Array of Migration class
     */
    public static function drop($migrations)
    {
        $instance = self::getMigrationInstances($migrations);

        foreach ($instance as $migration) {
            if (method_exists($migration, 'down')) {
                $migration->down();
            }
        }
    }

    /**
     * Create instances for migrations
     *
     * @param array $migrations
     *
     * @return array<int, Migration>
     */
    public static function getMigrationInstances($migrations)
    {
        $basePath            = $migrations['path'];
        $migrationClassNames = $migrations['migrations'];

        $migrationClasses = [];
        foreach ($migrationClassNames as $migration) {
            if (is_readable($basePath . $migration . '.php')) {
                if (!class_exists($migration)) {
                    include $basePath . $migration . '.php';
                }

                $migrationClasses[] = new $migration();
            }
        }

        return $migrationClasses;
    }

    /**
     * Migration Iterator.
     *
     * @return [Migration]
     */
    public static function getMigrations()
    {
        $migrationsPath = Config::get('BASEDIR')
            . DIRECTORY_SEPARATOR
            . 'db'
            . DIRECTORY_SEPARATOR
            . 'Migrations'
            . DIRECTORY_SEPARATOR;

        $migrations = new FilesystemIterator(
            $migrationsPath,
            FilesystemIterator::SKIP_DOTS
        );
        $migrationClasses = [];
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
                if (!class_exists($migrationName)) {
                    include $migration->getPathname();
                }

                $migrationClasses[\intval(preg_split('/_/', $migration->getBasename())[0])] = new $migrationName();
            }
        }

        ksort($migrationClasses);

        return $migrationClasses;
    }
}
