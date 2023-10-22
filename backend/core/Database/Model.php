<?php

/**
 * Provides Base Model Class.
 */

namespace BitApps\FM\Core\Database;

use ArrayAccess;
use BitApps\FM\Core\Helpers\JSON;

use DateTime;

use JsonSerializable;
use ReturnTypeWillChange;
use RuntimeException;

/**
 * Abstract class for model.
 *
 * @method static QueryBuilder         from($_from)
 * @method static Model                getModel()
 * @method static QueryBuilder         queryFor($_for)
 * @method static QueryBuilder         newQuery()
 * @method static void                 addBindings($bindings)
 * @method static array                getBindings()
 * @method static Model|array<int,     Model>|false all($columns = ['*'])
 * @method static QueryBuilder         select($columns = ['*'])
 * @method static Model|array<int,     Model>|false get($columns = ['*'])
 * @method static Model|bool           first()
 * @method static Model|array<int,     Model>|false find($attributes)
 * @method static Model|bool           findOne($attributes)
 * @method static string|string[]|null getConditions(QueryBuilder $query, $type = 'where')
 * @method static string               prepareOperatorForWhere($clause)
 * @method static QueryBuilder         where(...$params)
 * @method static QueryBuilder         whereIn(...$params)
 * @method static QueryBuilder         orWhere(...$params)
 * @method static QueryBuilder         whereRaw($sql, $bindings = [])
 * @method static QueryBuilder         whereNull($column)
 * @method static QueryBuilder         whereNotNull($column)
 * @method static QueryBuilder         whereBetween($column, $start, $end)
 * @method static QueryBuilder         orWhereBetween($column, $start, $end)
 * @method static QueryBuilder         groupBy($columns)
 * @method static QueryBuilder         having(...$params)
 * @method static QueryBuilder         orHaving(...$params)
 * @method static QueryBuilder         join($table, $first_column, $operator = null, $second_column = null, $type = 'INNER')
 * @method static QueryBuilder         leftJoin($table, $first_column, $operator = null, $second_column = null)
 * @method static QueryBuilder         rightJoin($table, $first_column, $operator = null, $second_column = null)
 * @method static QueryBuilder         fullJoin($table, $first_column, $operator = null, $second_column = null)
 * @method static QueryBuilder         crossJoin($table, $first_column, $operator = null, $second_column = null)
 * @method static QueryBuilder         on($first_column, $operator = null, $second_column = null, $bool = 'AND')
 * @method static QueryBuilder         orOn($first_column, $operator = null, $second_column = null)
 * @method static string               getOrderBy()
 * @method static QueryBuilder         orderBy($column)
 * @method static QueryBuilder         asc()
 * @method static QueryBuilder         desc()
 * @method static object|null          raw($sql, $bindings = [])
 * @method static QueryBuilder         take($count)
 * @method static QueryBuilder         skip($count)
 * @method static QueryBuilder         getOffset()
 * @method static Model|array<int,     Model>|false insert($attributes = [])
 * @method static QueryBuilder         update($attributes = [])
 * @method static string|bool          destroy($ids = [])
 * @method static bool                 save()
 * @method static QueryBuilder         withCount()
 * @method static null|int             count()
 * @method static bool|string          delete()
 * @method static QueryBuilder         with($relation)
 * @method static void                 startTransaction()
 * @method static void                 commit()
 * @method static void                 rollback()
 * @method static string               prepare($sql = null)
 */
abstract class Model implements ArrayAccess, JsonSerializable
{
    use Relations;

    public $timestamps = true;

    protected $table;

    protected $primaryKey;

    protected $fillable;

    protected $casts;

    protected $prefix = '';

    protected $attributes = [];

    protected $dirty = [];

    private static $_instance;

    private $_tableWithoutPrefix;

    private $_relateAs;

    private $_original = [];

    private $_isExists;

    /**
     * QueryBuilder instance.
     *
     * @var QueryBuilder
     */
    private $_queryBuilder;

    /**
     * Undocumented function.
     *
     * @param mixed $attributes
     */
    public function __construct($attributes = [])
    {
        $this->prefix = Connection::getPrefix();
        if (!property_exists($this, 'table') || !$this->table) {
            $this->_tableWithoutPrefix = ltrim(
                strtolower(
                    preg_replace(
                        '/[A-Z]([A-Z](?![a-z]))*/',
                        '_$0',
                        basename(str_replace('\\', '/', static::class))
                    )
                ),
                '_'
            ) . 's';
        }

        $this->table = $this->prefix . $this->_tableWithoutPrefix;

        if (!isset($this->primaryKey)) {
            $this->primaryKey = 'id';
        }

        if (\is_array($attributes)) {
            $this->fill($attributes);
        } else {
            $this->setAttribute($this->getPrimaryKey(), $attributes);
            $this->refresh();
        }
    }

    public function __isset($offset)
    {
        return isset($this->attributes[$offset]);
    }

    public function __get($offset)
    {
        if (property_exists($this->getQueryBuilder(), $offset)) {
            return $this->getQueryBuilder()->{$offset};
        }

        return $this->getAttribute($offset);
    }

    public function __set($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    public function __unset($offset)
    {
        $this->unsetAttribute($offset);
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->getQueryBuilder(), $method)) {
            return \call_user_func_array([$this->getQueryBuilder(), $method], $parameters);
        }

        throw new RuntimeException('Undefined method [' . esc_html($method) . '] called on Model class.');
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static())->{$method}(...$parameters);
    }

    public function __clone()
    {
        $this->setExists(false);
        $this->setRelateAs('');
        $this->dirty      = null;
        $this->attributes = [];
        $this->_original  = [];
    }

    public function fill($attributes, $force = false)
    {
        foreach ($attributes as $key => $value) {
            if ($force || !isset($this->fillable) || (isset($this->fillable) && \in_array($key, $this->fillable))) {
                $this->setAttribute($key, $this->castTo($key, $value));
            }
        }
    }

    public function refresh()
    {
        if (!isset($this->attributes[$this->primaryKey])) {
            return false;
        }

        $result = $this->newQuery()->findOne([$this->primaryKey => $this->attributes[$this->primaryKey]]);

        if (!$result) {
            $this->_isExists = false;
        } else {
            $this->_isExists = true;
        }

        return $this->_isExists;
    }

    public function exists()
    {
        if (!isset($this->_isExists)) {
            $this->refresh();
        }

        return $this->_isExists;
    }

    public function setExists($status)
    {
        $this->_isExists = $status;
    }

    public function setAttribute($key, $value)
    {
        if (
            $this->_isExists
            && (!isset($this->_original[$key])
                || isset($this->_original[$key]) && $this->_original[$key] != $value
            )
        ) {
            $this->dirty[$key] = $value;
        }

        $this->attributes[$key] = $value;
    }

    public function unsetAttribute($key)
    {
        unset($this->attributes[$key]);
    }

    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    public function getForeignKey()
    {
        return $this->getTableWithoutPrefix() . '_' . $this->primaryKey;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getTableWithoutPrefix()
    {
        return $this->_tableWithoutPrefix;
    }

    public function setRelateAs($relation)
    {
        $this->_relateAs = $relation;

        return $this;
    }

    public function getRelateAs()
    {
        return $this->_relateAs;
    }

    public function getFillable()
    {
        if (!isset($this->fillable)) {
            $allAttributes = $this->getAttributes();
            unset(
                $allAttributes[$this->primaryKey],
                $allAttributes['created_at'],
                $allAttributes['updated_at'],
                $allAttributes['deleted_at']
            );
            $this->fillable = array_keys($allAttributes);
        }

        return $this->fillable;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->attributes[$offset];
        }

        if (method_exists($this, $offset)) {
            $this->setAttribute($offset, $this->processRelatedAttribute($this->{$offset}()));

            return $this->attributes[$offset];
        }

        if (method_exists($this, 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $offset))) . 'Attribute')) {
            $this->setAttribute(
                $offset,
                $this->{'get'
                    . str_replace(' ', '', ucwords(str_replace('_', ' ', $offset))) . 'Attribute'}()
            );

            return $this->attributes[$offset];
        }
    }

    public function isDirty()
    {
        return !\is_null($this->dirty);
    }

    public function getDirtyAttributes()
    {
        return $this->dirty;
    }

    public function getOriginal()
    {
        return $this->_original;
    }

    public function getInstanceFromBuilder($result, $setAttribute = false)
    {
        if (\count($result) == 0) {
            return false;
        }

        $this->retrieveRelateData($this->getQueryBuilder());
        if (\count($result) == 1 && $setAttribute) {
            $this->fill((array) $result[0], true);
            $this->setExists(true);
            $this->setRelatedData($this);
            $this->setExists(true);

            return $this;
        }

        return array_map(
            function ($row) {
                $model = clone $this;
                $model->fill((array) $row, true);
                $this->setRelatedData($model);
                $model->setExists(true);

                return $model;
            },
            $result
        );
    }

    public function getQueryBuilder()
    {
        return isset($this->_queryBuilder) ? $this->_queryBuilder : $this->_queryBuilder = new QueryBuilder($this);
    }

    public function newQuery()
    {
        return new QueryBuilder($this);
    }

    #[ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        return isset($this->attributes[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    #[ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    #[ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->unsetAttribute($offset);
    }

    #[ReturnTypeWillChange]
    public function jsonSerialize()
    {
        if (!$this->exists()) {
            return [];
        }

        return $this->attributes;
    }

    private static function getInstance()
    {
        if (\is_null(self::$_instance)) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    private function castTo($column, $value)
    {
        if (
            !isset($this->casts)
            || (isset($this->casts) && !isset($this->casts[$column]))
            || !method_exists($this, 'castTo' . ucfirst($this->casts[$column]))
        ) {
            return $value;
        }

        return \call_user_func([$this, 'castTo' . ucfirst($this->casts[$column])], $value);
    }

    private function castToObject($value)
    {
        if (\is_string($value)) {
            return JSON::decode($value);
        }

        return (object) $value;
    }

    private function castToArray($value)
    {
        if (\is_string($value)) {
            return JSON::decode($value, true);
        }

        return (array) $value;
    }

    private function castToInt($value)
    {
        return (int) $value;
    }

    private function castToString($value)
    {
        return (string) $value;
    }

    private function castToDate($value)
    {
        return DateTime::createFromFormat('Y-m-d H:i:s', $value);
    }

    private function processRelatedAttribute(QueryBuilder $attribute)
    {
        $relation     = $attribute->getModel()->getRelateAs();
        $relationKey  = $attribute->getModel()->getRelationalKeys()[$relation];
        $attribute->where($relationKey['foreignKey'], $this->getAttribute($relationKey['localKey']));
        if ($relation == 'oneToOne') {
            return $attribute->first();
        }

        return $attribute->get();
    }
}
