<?php

/**
 * Class For Database Migration.
 *
 * @license GPL-2.0-or-later
 * Modified on 23-January-2024 using Strauss.
 * @see https://github.com/BrianHenryIE/strauss
 */

namespace BitApps\FM\Dependencies\BitApps\WPKit\Migration;

if (!\defined('ABSPATH')) {
    exit;
}

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * Database Migration.
 */
final class MigrationHelper
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
}
