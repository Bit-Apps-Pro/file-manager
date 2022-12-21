<?php
/**
 * Class For Database
 *
 * @category Database
 *
 * @author   Bit Code Developer <developer@bitcode.pro>
 */

namespace BitApps\FM\Core\Database;

/**
 * Helps to migrate tables on plugin activate
 */
abstract class Migration
{
    /**
     * Migrate tables
     *
     * @return void
     */
    abstract public function up();

    /**
     * Drop tables, columns
     *
     * @return void
     */
    abstract public function down();
}
