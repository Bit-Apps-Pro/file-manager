<?php

/**
 * Provides Base Model Class
 */

namespace BitApps\FM\Core\Database;

/**
 * Undocumented class
 */

use ArrayAccess;
use BitApps\FM\Core\Helpers\JSON;
use DateTime;
use JsonSerializable;
use RuntimeException;

abstract class Model implements ArrayAccess, JsonSerializable
{
    use Relations;

    private static $_intstance = null;

    private $_table_without_Prefix;

    private $_relate_as = null;

    private $_original = [];

    protected $table;

    private $_is_exists;

    protected $primary_key;

    protected $fillable;

    protected $casts;

    protected $prefix = '';

    protected $attributes = [];

    protected $dirty = [];

    public $timestamps = true;

    /**
     * QueryBuilder instance
     *
     * @var QueryBuilder
     */
    private $_queryBuilder;

    /**
     * Undocumented function
     */
    public function __construct($attributes = [])
    {
        $this->prefix = Connection::getPrefix();
        if (!property_exists($this, 'table') || !$this->table) {
            $this->_table_without_Prefix =  ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', basename(str_replace('\\', '/', get_class($this))))), '_') . "s";
        }
        $this->table = $this->prefix . $this->_table_without_Prefix;

        if (!isset($this->primary_key)) {
            $this->primary_key = 'id';
        }

        if (is_array($attributes)) {
            $this->fill($attributes);
        } else if (is_numeric($attributes)) {
            $this->setAttribute($this->getPrimaryKey(), $attributes);
        }
    }

    private static  function getInstance()
    {
        if (is_null(self::$_intstance)) {
            self::$_intstance = new static();
        }
        return self::$_intstance;
    }

    public function fill($attributes, $force = false)
    {
        foreach ($attributes as $key => $value) {
            if ($force || !isset($this->fillable) || (isset($this->fillable) && in_array($key, $this->fillable))) {
                $this->setAttribute($key, $this->castTo($key, $value));
            }
        }
    }

    private function castTo($column, $value)
    {
        if (
            !isset($this->casts)
            || (isset($this->casts) && !isset($this->casts[$column]))
            || !method_exists($this, "castTo" . ucfirst($this->casts[$column]))
        ) {
            return $value;
        }
        return call_user_func([$this, "castTo" . ucfirst($this->casts[$column])], $value);
    }

    private function castToObject($value)
    {
        if (is_string($value)) {
            return JSON::decode($value);
        }
        return (object) $value;
    }

    private function castToArray($value)
    {
        if (is_string($value)) {
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

    public function refresh()
    {
        if (!isset($this->attributes[$this->primary_key])) {
            return false;
        }

        $result = $this->newQuery()->findOne([$this->primary_key => $this->attributes[$this->primary_key]]);

        if (!$result) {
            $this->_is_exists = false;
        } else {
            $this->_is_exists = true;
        }

        return $this->_is_exists;
    }

    public function exists()
    {
        if (!isset($this->_is_exists)) {
            $this->refresh();
        }
        return $this->_is_exists;
    }

    public function setExists($status)
    {
        $this->_is_exists = $status;
    }

    public function setAttribute($key, $value)
    {
        if ($this->_is_exists && (!isset($this->_original[$key]) || isset($this->_original[$key]) && $this->_original[$key] != $value)) {
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
        return $this->primary_key;
    }

    public function getForeignKey()
    {
        return $this->getTableWithoutPrefix() . '_' . $this->primary_key;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function getTableWithoutPrefix()
    {
        return $this->_table_without_Prefix;
    }

    public function setRelateAs($relation)
    {
        $this->_relate_as = $relation;
        return $this;
    }

    public function getRelateAs()
    {
        return $this->_relate_as;
    }

    public function getFillable()
    {
        if (!isset($this->fillable)) {
            $attributes = $this->getAttributes();
            unset($attributes[$this->primary_key], $attributes['created_at'], $attributes['updated_at'], $attributes['deleted_at']);
            $this->fillable = array_keys($attributes);
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
        } else if (method_exists($this, $offset)) {
            $this->setAttribute($offset, $this->processRelatedAttribute($this->{$offset}()));
            return $this->attributes[$offset];
        } else if (method_exists($this, 'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $offset))) . 'Attribute')) {
            $this->setAttribute($offset, $this->{'get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $offset))) . 'Attribute'}());
            return $this->attributes[$offset];
        }
        return null;
    }

    private function processRelatedAttribute(QueryBuilder $attribute)
    {
        $relation = $attribute->getModel()->getRelateAs();
        $relation_key = $attribute->getModel()->getRelationalKeys()[$relation];
        $attribute->where($relation_key['foreignKey'], $this->getAttribute($relation_key['localKey']));
        if ($relation == 'oneToOne') {
            return $attribute->first();
        } else {
            return $attribute->get();
        }
    }

    public function isDirty()
    {
        return !is_null($this->dirty);
    }

    public function getOriginal()
    {
        return $this->_original;
    }

    public function getInstanceFromBuilder($result, $set_attribute = false)
    {
        if (count($result) == 0) {
            return false;
        }
        $this->retrieveRelateData($this->getQueryBuilder());
        if (count($result) == 1 && $set_attribute) {
            $this->fill((array) $result[0], true);
            $this->setRelatedData($this);
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



    public function __isset($offset)
    {
        return isset($this->attributes[$offset]);
    }

    #[\ReturnTypeWillChange]
    public function offsetExists($offset)
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

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->getAttribute($offset);
    }

    public function __set($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    #[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        $this->setAttribute($offset, $value);
    }

    public function __unset($offset)
    {
        $this->unsetAttribute($offset);
    }

    #[\ReturnTypeWillChange]
    public function offsetUnset($offset)
    {
        $this->unsetAttribute($offset);
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        return $this->attributes;
    }

    public function __call($method, $parameters)
    {
        if (method_exists($this->getQueryBuilder(), $method)) {
            return call_user_func_array([$this->getQueryBuilder(), $method], $parameters);
        }
        throw new RuntimeException('Undefined method [' . $method . '] called on Model class.');
    }

    public static function __callStatic($method, $parameters)
    {
        return (new static)->$method(...$parameters);
    }

    public function __clone()
    {
        $this->setExists(false);
        $this->setRelateAs('');
        $this->dirty = null;
        $this->attributes = [];
        $this->_original = [];
    }
}
