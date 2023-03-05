<?php

namespace BitApps\FM\Core\Database;

if (!\defined('ABSPATH')) {
    exit;
}

use Closure;

use Exception;

use RuntimeException;

/**
 * Class for schema blueprint.
 *
 * @method Blueprint char($name, $length = null)
 * @method Blueprint varchar($name, $length = null)
 * @method Blueprint binary($name, $length = null)
 * @method Blueprint json($name, $length = null)
 * @method Blueprint varbinary($name, $length = null)
 * @method Blueprint tinyblob($name, $length = null)
 * @method Blueprint tinytext($name, $length = null)
 * @method Blueprint text($name, $length = null)
 * @method Blueprint blob($name, $length = null)
 * @method Blueprint mediumtext($name, $length = null)
 * @method Blueprint mediumblob($name, $length = null)
 * @method Blueprint longtext($name, $length = null)
 * @method Blueprint longblob($name, $length = null)
 * @method Blueprint enum($name, array $enum)
 * @method Blueprint set($name, array $set)
 * @method Blueprint bit($name, $length = null)
 * @method Blueprint tinyint($name, $length = null)
 * @method Blueprint bool($name)
 * @method Blueprint boolean($name)
 * @method Blueprint smallint($name, $length = null)
 * @method Blueprint mediumint($name, $length = null)
 * @method Blueprint int($name, $length = null)
 * @method Blueprint integer($name, $length = null)
 * @method Blueprint bigint($name, $length = null)
 * @method Blueprint float($name, $length = null)
 * @method Blueprint float($name, $length = null)
 * @method Blueprint double($name, $length = null)
 * @method Blueprint double_precision($name, $length = null)
 * @method Blueprint decimal($name, $length = null)
 * @method Blueprint dec($name, $length = null)
 * @method Blueprint date($name)
 * @method Blueprint datetime($name)
 * @method Blueprint timestamp($name)
 * @method Blueprint time($name)
 * @method Blueprint year($name)
 */
class Blueprint
{
    public $charset;

    public $collation;

    public $after;

    protected $table;

    protected $primaryKey = [];

    protected $uniqueIndex = [];

    protected $indexColumns = [];

    protected $foreignKeys = [];

    protected $columns = [];

    protected $columnIndex = 0;

    protected $columnsToDrop = [];

    protected $columnsToRename = [];

    protected $method;

    private $_sql = '';

    private $_rawSql = false;

    private $_prefix = '';

    private $_edit = [];

    private $_fkID;

    private $_newName;

    /**
     * Create a new schema blueprint.
     *
     * @param string       $table    Table name
     * @param string       $method   Schema method
     * @param string       $prefix   Table prefix
     * @param null|Closure $callback Closure to build the blueprint
     */
    public function __construct($table, $method, $prefix = '', Closure $callback = null)
    {
        $this->_prefix   = $prefix;
        $this->table     = "{$prefix}{$table}";
        $this->method    = $method;
        $this->collation = $this->getCollation();
        if ($callback instanceof Closure) {
            $callback($this);
        }

        Connection::suppressError();
    }

    public function __call($method, $parameters)
    {
        $formattedMethodName = strtoupper(str_replace('_', ' ', $method));
        if ($this->isValidType($formattedMethodName)) {
            if (\count($parameters) > 2) {
                throw new Exception('Too many parameters');
            }

            $columnName = $parameters[0];
            array_shift($parameters);

            return $this->addColumn($columnName, $formattedMethodName, ...$parameters);
        }

        throw new RuntimeException("Undefined method [  {$method}  ] called on Blueprint");
    }

    public function build()
    {
        return $this->toSql();
    }

    public function toSql()
    {
        switch ($this->method) {
            case 'create':
                $this->create(true);

                break;

            case 'drop':
                $this->drop();

                break;

            default:
                $this->_rawSql = true;

                break;
        }

        return $this->execute();
    }

    public function execute()
    {
        if (\is_null($this->_sql)) {
            return false;
        }

        Connection::query($this->_sql);
        $hasError = Connection::prop('last_error');
        Connection::restoreErrorState();
        if ($hasError) {
            throw new RuntimeException($hasError);
        }

        return true;
    }

    public function create($isSql = false)
    {
        if ($isSql) {
            if (empty($this->columns)) {
                return false;
            }

            $queryToAdd[] = $this->addColumnQuery();
            $queryToAdd[] = $this->addPrimaryKeyQuery();
            $queryToAdd[] = $this->addUniqueIndexQuery();
            $queryToAdd[] = $this->addIndexQuery();
            $queryToAdd[] = $this->addForeignKeyQuery();

            $this->_sql = "CREATE TABLE IF NOT EXISTS {$this->table} (
             {$this->processQueryArr($queryToAdd)}
             ) {$this->collation}";
        }

        return $this;
    }

    public function edit()
    {
        $queryToAdd[] = $this->addColumnQuery();
        $queryToAdd[] = $this->dropColumnQuery();
        $queryToAdd   = $queryToAdd + $this->_edit;
        $queryToAdd[] = $this->addPrimaryKeyQuery();
        $queryToAdd[] = $this->addUniqueIndexQuery();
        $queryToAdd[] = $this->addIndexQuery();
        $queryToAdd[] = $this->addForeignKeyQuery();

        $query      = $this->processQueryArr($queryToAdd);
        $this->_sql = "ALTER TABLE {$this->table}";
        $this->_sql .= " {$query}";

        return $this;
    }

    public function drop()
    {
        if ($this->method === 'drop') {
            $this->_sql = "DROP TABLE IF EXISTS {$this->table}";
        }

        return $this;
    }

    public function rename($newName)
    {
        if ($this->method === 'rename') {
            $this->_sql = "ALTER TABLE {$this->table} RENAME TO {$this->_prefix}{$newName}";
        } else {
            $this->_newName = $newName;
        }

        return $this;
    }

    public function addColumn($name, $type, $length = null)
    {
        if ($this->method === 'addColumn') {
            $this->_sql = "ALTER TABLE {$this->table} ADD {$name} {$type}";
            if ($length) {
                $this->_sql .= "({$length})";
            }
        } else {
            $this->columnIndex = \count($this->columns);
            $this->columns[]   = [
                'name' => $name,
                'type' => $type,
            ];
            if (!\is_null($length)) {
                $this->length($length);
            }
        }

        return $this;
    }

    public function dropColumn($column)
    {
        if ($this->method === 'dropColumn') {
            $this->_sql = "ALTER TABLE {$this->table} DROP {$column}";
        } else {
            $this->columnsToDrop[] = $column;
        }

        return $this;
    }

    public function renameColumn($column, $newName)
    {
        if ($this->method === 'renameColumn') {
            $this->_sql = "ALTER TABLE {$this->table} CHANGE {$column} {$newName}";
        } else {
            $this->columnsToRename[] = [
                'column'   => $column,
                'new_name' => $newName,
            ];
        }

        return $this;
    }

    public function renameColumnQuery()
    {
        $query = '';
        if (\is_array($this->columnsToRename)) {
            foreach ($this->columnsToRename as $id => $column) {
                if ($id > 0) {
                    $query .= "\n, ";
                }

                $query .= "CHANGE {$column['column']} {$column['new_name']}";
            }
        }

        return $query;
    }

    public function change()
    {
        $this->columns[$this->columnIndex]['change'] = true;

        return $this;
    }

    public function increments($column = null)
    {
        if (!\is_null($column)) {
            $this->columnIndex = \count($this->columns);
            $this->columns[]   = [
                'type' => 'BIGINT',
                'name' => "{$column}",
            ];
        }

        $this->columns[$this->columnIndex]['props'][] = 'AUTO_INCREMENT';

        return $this;
    }

    public function id()
    {
        $this->columnIndex = \count($this->columns);
        $this->columns[]   = [
            'type'     => 'BIGINT',
            'name'     => 'id',
            'unsigned' => 'UNSIGNED',
        ];
        $this->increments();
        $this->primary();

        return $this;
    }

    public function string($name)
    {
        $this->columnIndex = \count($this->columns);
        $this->columns[]   = [
            'type' => 'VARCHAR',
            'name' => $name,
        ];
        $this->length('255');

        return $this;
    }

    public function timestamps()
    {
        $this->columnIndex = \count($this->columns);
        $this->columns[]   = [
            'type' => 'timestamp',
            'name' => 'created_at',
        ];
        $this->nullable()->defaultValue('NULL');
        ++$this->columnIndex;
        $this->columns[] = [
            'type' => 'timestamp',
            'name' => 'updated_at',
        ];
        $this->nullable()->defaultValue('NULL');

        return $this;
    }

    public function softDeletes()
    {
        $this->columnIndex = \count($this->columns);
        $this->columns[]   = [
            'type' => 'timestamp',
            'name' => 'deleted_at',
        ];
        $this->nullable()->defaultValue('NULL');

        return $this;
    }

    public function nullable()
    {
        $this->columns[$this->columnIndex]['nullable'] = true;

        return $this;
    }

    public function defaultValue($value)
    {
        $this->columns[$this->columnIndex]['default'] = $value;

        return $this;
    }

    public function unsigned()
    {
        $this->columns[$this->columnIndex]['unsigned'] = 'UNSIGNED';

        return $this;
    }

    public function zeroFill()
    {
        $this->columns[$this->columnIndex]['props'][] = 'ZEROFILL';

        return $this;
    }

    public function binary()
    {
        $this->columns[$this->columnIndex]['props'][] = 'BINARY';

        return $this;
    }

    public function primary()
    {
        $this->primaryKey[]                            = $this->columns[$this->columnIndex]['name'];
        $this->columns[$this->columnIndex]['nullable'] = false;

        return $this;
    }

    public function index($type = null)
    {
        $indexColumns = [
            'name' => $this->columns[$this->columnIndex]['name'],
        ];
        if (!\is_null($type)) {
            $indexColumns['type'] = $type;
        }

        $this->indexColumns[] = $indexColumns;

        return $this;
    }

    public function unique()
    {
        $this->uniqueIndex[] = $this->columns[$this->columnIndex]['name'];

        return $this;
    }

    public function foreign($ref, $refCol)
    {
        $this->_fkID          = \count($this->foreignKeys);
        $this->foreignKeys[]  = [
            'column'  => $this->columns[$this->columnIndex]['name'],
            'ref'     => $this->_prefix . $ref,
            'ref_col' => $refCol,
        ];

        return $this;
    }

    public function onDelete()
    {
        $this->foreignKeys[$this->_fkID]['method'] = 'onDelete';

        return $this;
    }

    public function onUpdate()
    {
        $this->foreignKeys[$this->_fkID]['method'] = 'onUpdate';

        return $this;
    }

    public function restrict()
    {
        if (\array_key_exists('method', $this->foreignKeys[$this->_fkID])) {
            $this->foreignKeys[$this->_fkID][$this->foreignKeys[$this->_fkID]['method']] = 'RESTRICT';
        } else {
            $this->foreignKeys[$this->_fkID]['both'] = 'RESTRICT';
        }

        return $this;
    }

    public function setNull()
    {
        if (\array_key_exists('method', $this->foreignKeys[$this->_fkID])) {
            $this->foreignKeys[$this->_fkID][$this->foreignKeys[$this->_fkID]['method']] = 'SET NULL';
        } else {
            $this->foreignKeys[$this->_fkID]['both'] = 'SET NULL';
        }

        return $this;
    }

    public function cascade()
    {
        if (\array_key_exists('method', $this->foreignKeys[$this->_fkID])) {
            $this->foreignKeys[$this->_fkID][$this->foreignKeys[$this->_fkID]['method']] = 'CASCADE';
        } else {
            $this->foreignKeys[$this->_fkID]['both'] = 'CASCADE';
        }

        return $this;
    }

    public function dropForeign($keys)
    {
        if ($this->method === 'dropForeign') {
            $this->_sql = "ALTER TABLE `{$this->table}`";
        } else {
            $sql     = '';
            $idCount = \count($keys) - 1;
            $i       = 0;
            if (\is_array($keys)) {
                foreach ($keys as $key) {
                    if ($i == $idCount) {
                        $sql .= " DROP FOREIGN KEY `{$key}`";
                    } else {
                        $sql .= " DROP FOREIGN KEY `{$key}`,";
                    }

                    $i++;
                }
            } else {
                $sql .= " DROP FOREIGN KEY `{$keys}`";
            }

            $this->_edit['dropForeign'] = $sql;
        }

        return $this;
    }

    public function dropIndex($indexes)
    {
        if ($this->method === 'dropIndex') {
            $this->_sql = "ALTER TABLE `{$this->table}`";
        } else {
            $sql     = '';
            $idCount = \count($indexes) - 1;
            $i       = 0;
            if (\is_array($indexes)) {
                foreach ($indexes as $index) {
                    if ($i == $idCount) {
                        $sql .= " DROP INDEX `{$index}`";
                    } else {
                        $sql .= " DROP INDEX `{$index}`,";
                    }

                    $i++;
                }
            } else {
                $sql .= " DROP INDEX `{$indexes}`";
            }

            $this->_edit['dropIndex'] = $sql;
        }

        return $this;
    }

    public function dropPrimary()
    {
        if ($this->method === 'dropPrimary') {
            $this->_sql = "ALTER TABLE `{$this->table}`";
        } else {
            $this->_edit['dropPrimary'] = ' DROP PRIMARY KEY';
        }

        return $this;
    }

    public function dropUnique($indexes)
    {
        return $this->dropIndex($indexes);
    }

    public function dropTimestamps()
    {
        if ($this->method === 'dropTimestamps') {
            $this->_sql = "ALTER TABLE `{$this->table}`";
        } else {
            $this->_edit['dropTimestamps'] = ' DROP COLUMN created_at, DROP COLUMN updated_at';
        }

        return $this;
    }

    public function length($length)
    {
        if (\is_array($length)) {
            $l = '';
            foreach ($length as $e) {
                $l .= "'{$e}',";
            }

            $length = rtrim($l, ',');
        }

        $this->columns[$this->columnIndex]['length'] = $length;

        return $this;
    }

    private function getCollation()
    {
        $collate = null;
        if (Connection::has_cap('collation')) {
            if (!empty(Connection::prop('charset'))) {
                $collate .= 'DEFAULT CHARACTER SET ' . Connection::prop('charset');
            }

            if (!empty(Connection::prop('collate'))) {
                $collate .= ' COLLATE ' . Connection::prop('collate');
            }
        }

        return $collate;
    }

    private function processQueryArr($queriesToAdd)
    {
        $query = '';
        foreach ($queriesToAdd as $key => $queryToAdd) {
            if (empty($queryToAdd)) {
                continue;
            }

            $query .= empty($query) || $query[\strlen($query) - 1] === ',' ? '' : ',';
            $query .= $queryToAdd;
        }

        return $query;
    }

    private function addColumnQuery()
    {
        if (!\is_array($this->columns)) {
            return '';
        }

        $query = '';
        foreach ($this->columns as $id => $column) {
            if ($id > 0) {
                $query .= "\n, ";
            }

            if ($this->method === 'edit') {
                $query .= 'ADD COLUMN ';
            }

            if (isset($column['change'])) {
                $query .= 'CHANGE COLUMN ';
            }

            $query .= $column['name'] . ' ' . $column['type'];
            if (!empty($column['length'])) {
                $query .= '(' . $column['length'] . ')';
            } elseif (!empty($column['precision']) && !empty($column['scale'])) {
                $query .= '(' . $column['precision'] . ', ' . $column['scale'] . ')';
            } else {
                $query .= ' ';
            }

            if (!empty($column['unsigned'])) {
                $query .= " {$column['unsigned']}";
            }

            if (!empty($column['nullable'])) {
                $query .= ' NULL';
            } else {
                $query .= ' NOT NULL';
            }

            if (\array_key_exists('default', $column)) {
                if (!\in_array($column['default'], ['CURRENT_TIMESTAMP', 'NULL']) && !\is_int($column['default'])) {
                    $column['default'] = "'{$column['default']}'";
                }

                $query .= ' DEFAULT ' . $column['default'];
            }

            if (!empty($column['props'])) {
                $query .= ' ' . implode(' ', $column['props']);
            }
        }

        return $query;
    }

    private function dropColumnQuery()
    {
        if (!\is_array($this->columnsToDrop)) {
            return '';
        }

        $query = '';
        foreach ($this->columnsToDrop as $id => $column) {
            if ($id > 0) {
                $query .= "\n, ";
            }

            $query .= "DROP {$column}";
        }

        return $query;
    }

    private function addPrimaryKeyQuery()
    {
        if (!\is_array($this->primaryKey) || \count($this->primaryKey) === 0) {
            return '';
        }

        $query = "\n";
        if ($this->method === 'edit') {
            $query .= ' ADD ';
        }

        $query .= "PRIMARY KEY (\n";
        $query .= implode(', ', $this->primaryKey);
        $query .= "\n)";

        return $query;
    }

    private function addIndexQuery()
    {
        if (!\is_array($this->indexColumns) || \count($this->indexColumns) === 0) {
            return '';
        }

        $query = '';
        foreach ($this->indexColumns as $key => $indexColumn) {
            if ($key > 0) {
                $query .= "\n, ";
            }

            if ($this->method === 'edit') {
                $query .= ' ADD ';
            }

            $query .= (isset($indexColumn['type']) ? $indexColumn['type'] : null
            ) . "INDEX {$indexColumn['name']}_INDEX ({$indexColumn['name']} ASC)";
        }

        return $query;
    }

    private function addUniqueIndexQuery()
    {
        if (!\is_array($this->uniqueIndex) || \count($this->uniqueIndex) === 0) {
            return '';
        }

        $query = '';
        foreach ($this->uniqueIndex as $key => $uniqueColumn) {
            $query .= "\nUNIQUE INDEX {$uniqueColumn}_UNIQUE ({$uniqueColumn} ASC) VISIBLE,";
        }

        return $query;
    }

    private function addForeignKeyQuery()
    {
        if (!\is_array($this->foreignKeys) || \count($this->foreignKeys) === 0) {
            return '';
        }

        $query = '';
        foreach ($this->foreignKeys as $_fkId => $foreignKey) {
            /* $query .= "\nCONSTRAINT f_c_{$this->table}_{$_fkId} "
                ." FOREIGN KEY f_key_{$this->table}_{$_fkId} ({$foreignKey['column']})"
                ." REFERENCES {$foreignKey['ref']} ({$foreignKey['ref_col']})"
                . (isset($foreignKey['onUpdate']) ? " ON DELETE {$foreignKey['onUpdate']}" : null)
                . (isset($foreignKey['onUpdate']) ? " ON UPDATE {$foreignKey['onUpdate']}" : null)
                . (isset($foreignKey['both']) ? " ON DELETE {$foreignKey['both']} ON UPDATE {$foreignKey['both']}" : null)
                . ","; */

            $query .= " FOREIGN KEY ({$foreignKey['column']}) REFERENCES {$foreignKey['ref']} ";
            $query .= "({$foreignKey['ref_col']})";
            $query .= (isset($foreignKey['onDelete']) ? " ON DELETE {$foreignKey['onDelete']}" : null);
            $query .= (isset($foreignKey['onUpdate']) ? " ON UPDATE {$foreignKey['onUpdate']}" : null);
            $query .= (isset($foreignKey['both']) ? " ON DELETE {$foreignKey['both']} ON UPDATE {$foreignKey['both']}" : null);
            $query .= ',';
        }

        return rtrim($query, ',');
    }

    private function isValidType($type)
    {
        // phpcs:disable Squiz.Arrays.ArrayDeclaration.ValueNoNewline
        return \in_array(
            $type,
            [
                'CHAR', 'VARCHAR', 'BINARY', 'JSON',
                'VARBINARY', 'TINYBLOB', 'TINYTEXT', 'TEXT', 'BLOB',
                'MEDIUMTEXT', 'MEDIUMBLOB', 'LONGTEXT', 'LONGBLOB',
                'ENUM', 'SET', 'BIT', 'TINYINT', 'BOOL', 'BOOLEAN', 'SMALLINT',
                'MEDIUMINT', 'INT', 'INTEGER', 'BIGINT', 'FLOAT',
                'FLOAT', 'DOUBLE', 'DOUBLE PRECISION', 'DECIMAL', 'DEC', 'DATE', 'DATETIME', 'TIMESTAMP', 'TIME', 'YEAR',
            ]
        );
    }
}
