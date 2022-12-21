<?php

namespace BitApps\FM\Core\Database;

use BitApps\FM\Core\Helpers\JSON;
use Closure;

class QueryBuilder
{
    private const UPDATE = 'Update';
    private const INSERT = 'Insert';
    private const DELETE = 'Delete';
    private const SELECT = 'Select';

    private $_model;

    private $_from;

    private $_for;

    private $_method;

    protected $table;

    protected $limit;

    protected $offset;

    protected $orderBy = [];

    protected $order;

    protected $where = [];

    protected $params = [];

    protected $joins = [];

    protected $groupBy = [];

    protected $having = [];

    protected $bindings = [];

    protected $select = [];

    protected $insert = [];

    protected $update = [];

    protected $distinct = false;

    protected $columns = ['*'];

    protected $raw = '';

    public $last_error;

    public $last_query;

    public $last_result;

    public function __construct(Model $model)
    {
        $this->_model = $model;
        $this->table = $model->getTable();
    }

    public function from($_from)
    {
        $this->_from = $_from;
        return $this;
    }
    
    public function getModel()
    {
        return $this->_model;
    }

    private function getFrom()
    {
        return isset($this->_from) ? " {$this->_from}" : null;
    }


    public function for($_for)
    {
        $this->_for = $_for;
        return $this;
    }

    public function newQuery()
    {
        return new QueryBuilder($this->_model);
    }

    public function addBindings($bindings)
    {
        if (is_null($bindings)) {
            return;
        }
        if (!is_array($bindings)) {
            $bindings = [$bindings];
        }
        $this->bindings = array_merge($this->bindings, $bindings);
    }

    public function getBindings()
    {
        return $this->bindings;
    }

    public function all($columns = ['*'])
    {
        return $this->get($columns);
    }

    public function select($columns = ['*'])
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }
        $this->select = $columns;
        return $this;
    }

    /**
     * Prepare the query and execute.
     * 
     * @param array $columns 
     * 
     * @return Model| [Model] | false
     */
    public function get($columns = ['*'])
    {
        $columns = isset($columns) && is_array($columns) ? $columns : func_get_args();
        $this->select = $columns;
        $this->_method = self::SELECT;
        return $this->_model->getInstanceFromBuilder(
            $this->exec(),
            $this->limit == 1 ? true : false
        );
    }

    public function first()
    {
        return $this->take(1)->get();
    }

    public function find($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->where($key, $value);
        }

        return $this->get();
    }

    public function findOne($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->where($key, $value);
        }

        return $this->first();
    }

    public function getConditions(QueryBuilder $query, $type = 'where')
    {
        return $this->processConditions($query->{$type}, $type);
        $sql = '';
        if (!empty($query->{$type})) {
            foreach ($query->{$type} as  $clause) {
                if (isset($clause['bool'])) {
                    $sql .= " " . $clause['bool'];
                } else {
                    $sql .= " AND";
                }
                if (isset($clause['raw'])) {
                    $sql .= " " . $clause['raw'];
                    $query->addBindings($clause['bindings']);
                    continue;
                }

                if (isset($clause['query'])) {
                    $sql .= " (" . $clause['query']->getConditions($clause['query'], $type) . ")";
                    $query->addBindings($clause['query']->getBindings());
                    continue;
                }
                $sql .= $query->prepareColumnForWhere($clause);
                $sql .= $query->prepareOperatorForWhere($clause);
                $sql .= $query->prepareValueForWhere($clause, $query);
            }
        }
        $sql = $query->removeLeadingbool($sql);
        return $sql;
    }

    protected function processConditions($conditions, $type = null)
    {
        $sql = '';
        if (is_array($conditions) && count($conditions) > 0) {
            foreach ($conditions as $clause) {
                if (isset($clause['bool'])) {
                    $sql .= " " . $clause['bool'];
                } else {
                    $sql .= " AND";
                }
                if (isset($clause['raw'])) {
                    $sql .= " " . $clause['raw'];
                    $this->addBindings($clause['bindings']);
                    continue;
                }

                if (isset($clause['query']) && !is_null($type)) {
                    $sql .= " (" . $clause['query']->getConditions($clause['query'], $type) . ")";
                    $this->addBindings($clause['query']->getBindings());
                    continue;
                }
                $sql .= $this->prepareColumnForWhere($clause);
                $sql .= $this->prepareOperatorForWhere($clause);
                $sql .= $this->prepareValueForWhere($clause, $this);
            }
            $sql = $this->removeLeadingbool($sql);
        }
        return $sql;
    }

    private function prepareColumnForWhere($clause)
    {
        if (isset($clause['column'])) {
            return " " . $clause['column'];
        }
        return null;
    }

    public function prepareOperatorForWhere($clause)
    {
        $sql = '';
        if (!isset($clause['column'])) {
            return $sql;
        }

        if (isset($clause['operator'])) {
            $sql .= " " . $clause['operator'];
        } elseif (is_array($clause['value'])) {
            $sql .= " IN ";
        } elseif (is_null($clause['value'])) {
            $sql = " IS NULL";
        } else {
            $sql .= " = ";
        }

        return $sql;
    }

    private function prepareValueForWhere($clause, self $query)
    {
        $sql = '';
        if (isset($clause['second_column'])) {
            return " " . $clause['second_column'];
        }

        if (!isset($clause['value'])) {
            return $sql;
        }

        if (is_array($clause['value'])) {
            $sql .= " (";
            foreach ($clause['value'] as $value) {
                $sql .= $this->getValueType($value) . ",";
                $query->addBindings($value);
            }
            $sql = rtrim($sql, ',') . ")";
        } elseif (isset($clause['operator']) && strpos($clause['operator'], 'IS') !== false) {
            $sql .= " " . $clause['value'];
        } elseif (isset($clause['operator']) && strtoupper($clause['operator']) === 'LIKE') {
            $sql .= " %s";
            $query->addBindings($clause['value']);
        } elseif (!is_null($clause['value'])) {
            $sql .= " " . $query->getValueType($clause['value']);
            $query->addBindings($clause['value']);
        }
        return $sql;
    }

    protected function prepareConditonal($params, $bool = 'AND', $type = 'where',)
    {
        $no_of_params = count($params);
        $conditions = [];
        if ($no_of_params === 0) {
            return $conditions;
        }
        if (func_num_args() == 1 && is_array($params[0])) {
            foreach ($params[0] as $clause) {
                if ($type === 'where') {
                    $this->prepareWhere($clause, $bool);
                }
            }
            return;
        }
        $conditions['bool'] = $bool;
        if ($params[0] instanceof Closure) {
            $nestedQuery = $this->newQuery()->for($type);
            call_user_func($params[0], $nestedQuery);
            $conditions['query'] = $nestedQuery;
            if (isset($params[1])) {
                $conditions['bool'] = $params[1];
            }
        } elseif ($no_of_params == 2) {
            $conditions['column'] = $params[0];
            $conditions['value'] = $params[1];
        } elseif ($no_of_params == 3) {
            $conditions['column'] = $params[0];
            $conditions['operator'] = $params[1];
            $conditions['value'] = $params[2];
        } elseif ($no_of_params == 4) {
            $conditions['column'] = $params[0];
            $conditions['operator'] = $params[1];
            $conditions[$type === 'where' ? 'value' : 'second_column'] = $params[2];
            $conditions['bool'] = $params[3];
        }

        return $conditions;
    }


    protected function removeLeadingbool($sql)
    {
        return preg_replace('/and |or /i', '', $sql, 1);
    }

    protected function getWhere()
    {
        $sql = $this->getConditions($this);
        if (empty($sql)) {
            return '';
        }
        return " WHERE $sql";
    }

    protected function prepareWhere($params, $bool = 'AND')
    {
        $conditions = $this->prepareConditonal($params, $bool);
        if (!empty($conditions)) {
            $this->where[] = $conditions;
        }
    }

    public function where(...$params)
    {
        $this->prepareWhere($params);
        return $this;
    }

    public function orWhere(...$params)
    {
        $this->prepareWhere($params, 'OR');
        return $this;
    }

    public function whereRaw($sql, $bindings = [])
    {
        var_dump($this->_method);
        $this->where[] = [
            'raw' => $sql,
            'bindings' => $bindings
        ];
        return $this;
    }

    public function whereNull($column)
    {
        $this->where[] = [
            'column' => $column,
            'operator' => 'IS NULL'
        ];
        return $this;
    }

    public function whereNotNull($column)
    {
        $this->where[] = [
            'column' => $column,
            'operator' => 'IS NOT NULL'
        ];
        return $this;
    }

    public function whereBetween($column, $start, $end)
    {
        $this->where[] = [
            'raw' =>  ' (' . $column . ' BETWEEN ' . $this->getValueType($start) . ' AND '  . $this->getValueType($end) . ')',
            'bindings' => [$start, $end]
        ];
        return $this;
    }

    public function orWhereBetween($column, $start, $end)
    {
        $this->where[] = [
            'raw' =>  ' (' . $column . ' BETWEEN ' . $this->getValueType($start) . ' AND '  . $this->getValueType($end) . ')',
            'bindings' => [$start, $end],
            'bool' => 'OR'
        ];
        return $this;
    }


    public function groupBy($columns)
    {
        $columns = is_array($columns) ? $columns : func_get_args();
        $this->groupBy = array_merge($this->groupBy, $columns);
        return $this;
    }

    protected function getGroupBy()
    {
        if (empty($this->groupBy)) {
            return '';
        }
        return ' GROUP BY ' . implode(',', $this->groupBy);
    }


    public function having(...$params)
    {
        return $this->prepareHaving($params);
    }

    public function orHaving(...$params)
    {
        return $this->prepareHaving($params, 'OR');
    }

    protected function prepareHaving($params, $bool = 'AND')
    {
        $conditions = $this->prepareConditonal($params, $bool, 'having');
        if (!empty($conditions)) {
            $this->having[] = $conditions;
        }
        return $this;
    }

    protected function getHaving()
    {
        $sql = $this->getConditions($this, 'having');
        if (empty($sql)) {
            return '';
        }
        return " HAVING $sql";
    }

    protected function getJoin()
    {
        $sql = '';
        if (empty($this->joins)) {
            return $sql;
        }

        foreach ($this->joins as $join) {
            $sql .= ' ' . $join['type'] . ' JOIN ' . $join['table'] . ' ON ' . $this->processConditions($join['on']);
        }
        return $sql;
    }

    public function join($table, $first_column, $operator = null, $second_column = null, $type = 'INNER')
    {
        $table = Connection::getPrefix() . $table;
        $hasAlias = preg_split('/ as /i', $table);
        if ($hasAlias && isset($hasAlias[1])) {
            $table = $hasAlias[0];
            $alias = $hasAlias[1];
        } else {
            $alias = $table;
        }
        $on[] = $this->prepareOn($alias, $first_column, $operator, $second_column, 'AND');
        $this->joins[] = [
            'table' => $table,
            'on' => $on,
            'type' => $type
        ];
        return $this;
    }

    public function leftJoin($table, $first_column, $operator = null, $second_column = null)
    {
        return $this->join($table, $first_column, $operator, $second_column, 'LEFT');
    }

    public function rightJoin($table, $first_column, $operator = null, $second_column = null)
    {
        return $this->join($table, $first_column, $operator, $second_column, 'RIGHT');
    }

    public function fullJoin($table, $first_column, $operator = null, $second_column = null)
    {
        return $this->join($table, $first_column, $operator, $second_column, 'FULL');
    }

    public function crossJoin($table, $first_column, $operator = null, $second_column = null)
    {
        return $this->join($table, $first_column, $operator, $second_column, 'CROSS');
    }

    public function on($first_column, $operator = null, $second_column = null, $bool = 'AND')
    {
        $join_index = count($this->joins) - 1;
        if ($join_index < 0) {
            $join_index = 0;
        }
        $table = $this->joins[$join_index]['table'];
        $this->joins[$join_index]['on'][] = $this->prepareOn($table, $first_column, $operator, $second_column, $bool);
        return $this;
    }

    public function orOn($first_column, $operator = null, $second_column = null)
    {
        return $this->on($first_column, $operator, $second_column, 'OR');
    }

    protected function prepareOn($table, $first_column, $operator, $second_column, $bool = 'AND')
    {
        if (is_null($operator) && is_null($second_column)) {
            $column = $this->_model->getTable() . '.' . $first_column;
            $second_column = $table . '.' . $first_column;
            $operator = '=';
        }
        return compact('column', 'operator', 'second_column', 'bool');
    }

    public function getOrderBy()
    {
        $sql = '';
        if (empty($this->orderBy)) {
            return $sql;
        }
        var_dump($this->orderBy);
        foreach ($this->orderBy as $order) {
            $sql .= $order['column'] . ' ' . $order['direction'] . ', ';
        }
        return ' ORDER BY ' . rtrim($sql, ', ');
    }

    public function orderBy($column)
    {
        $this->orderBy[] = [
            'column' => $column,
            'direction' => 'ASC'
        ];
        return $this;
    }

    public function asc()
    {
        $order_id = count($this->orderBy);
        if ($order_id > 0) {
            $order_id = $order_id - 1;
        }
        $this->orderBy[$order_id]['direction'] = 'ASC';
        if (!isset($this->orderBy[$order_id]['column'])) {
            $this->orderBy[$order_id]['column'] = $this->_model->getPrimaryKey();
        }
        return $this;
    }

    public function desc()
    {
        $order_id = count($this->orderBy);
        if ($order_id > 0) {
            $order_id = $order_id - 1;
        }
        $this->orderBy[$order_id]['direction'] = 'DESC';
        if (!isset($this->orderBy[$order_id]['column'])) {
            $this->orderBy[$order_id]['column'] = $this->_model->getPrimaryKey();
        }
        return $this;
    }


    public function raw($sql, $bindings = [])
    {
        $this->bindings = $bindings;

        return $this->exec($sql);
    }


    public function take($count)
    {
        $this->limit = $count;
        return $this;
    }

    private function getLimit()
    {
        return isset($this->limit) ? " LIMIT {$this->limit}" : "";
    }

    public function skip($count)
    {
        $this->offset = $count;
        return $this;
    }

    public function getOffset()
    {
        return isset($this->offset) ? " OFFSET {$this->offset}" : "";
    }

    public function insert($attributes = [])
    {
        $this->_model->fill($attributes);
        if ($this->save() ) {
            return $this->_model;
        }
        return false;
    }

    public function update($attributes = [])
    {
        $this->_method = 'Update';
        $this->_model->fill($attributes);
        $this->update = $this->prepareAttributeForSaveOrUpdate(true);
        return $this;
    }

    public function destroy($ids = [])
    {
        $this->_method = self::DELETE;
        $this->where($this->_model->getPrimaryKey(), $ids);
        return $this;
    }

    public function save()
    {
        $columns = $this->prepareAttributeForSaveOrUpdate($this->_model->exists());
        if ($this->_model->exists()) {
            var_dump($columns, $this->_model->exists());
            $this->where($this->_model->getPrimaryKey(), $this->_model->getAttribute($this->_model->getPrimaryKey()));
            $this->update = $columns;
            return $this->exec();
        }
        $this->insert = $columns;
        $this->exec();
        if ($insert_id = $this->lastInsertId()) {
            $this->_model->setAttribute($this->_model->getPrimaryKey(), $insert_id);
            return true;
        }
        return false;
    }

    private function prepareAttributeForSaveOrUpdate($is_update = false)
    {
        $columns = $this->_model->getFillable();
        if (property_exists($this->_model, 'timestamps') && $this->_model->timestamps) {
            if (!$is_update) {
                $this->_model->setAttribute('created_at', date('Y-m-d H:i:s'));
                $columns[] = 'created_at';
            }
            $this->_model->setAttribute('updated_at', date('Y-m-d H:i:s'));
            $columns[] = 'updated_at';
        }
        $attributes = $this->_model->getAttributes();
        foreach ($columns as $key => $column) {
            if (isset($attributes[$column])) {
                $this->bindings[] = JSON::maybeEncode($this->_model->{$column});
            } else {
                $this->bindings[] = '';
            }
        }

        if (isset($attributes[$this->_model->getPrimaryKey()]) && !in_array($this->_model->getPrimaryKey(), $columns)) {
            $columns[] = $this->_model->getPrimaryKey();
            $this->bindings[] = $attributes[$this->_model->getPrimaryKey()];
        }
        $this->_method = $is_update ? self::UPDATE : self::INSERT;
        return $columns;
    }

    private function prepareSelect()
    {
        $this->bindings = [];
        $sql = 'SELECT ' . implode(",", $this->select) . ' FROM ' . $this->table;
        $sql .= $this->getFrom();
        $sql .= $this->getJoin();
        $sql .= $this->getWhere($this);
        $sql .= $this->getGroupBy();
        $sql .= $this->getHaving();
        $sql .= $this->getOrderBy();
        $sql .= $this->getLimit();
        $sql .= $this->getOffset();
        return trim($sql);
    }
    
    public function withCount()
    {
        $this->select[] = 'COUNT(*) as count';
        return $this;
    }
    
    public function count()
    {
        $this->select = ['COUNT(*) as count'];
        $result = $this->exec($this->prepareSelect());
        return $result[0]->count;
    }



    private function prepareInsert()
    {
        $sql = 'INSERT INTO ' . $this->table;
        $sql .= ' (' . implode(', ', $this->insert) . ')';
        $sql .= ' VALUES (' .
            implode(
                ', ',
                array_map(
                    function ($value) {
                        return $this->getValueType($value);
                    },
                    $this->bindings
                )
            ) . ')';
        return $sql;
    }

    private function prepareUpdate()
    {
        $sql = 'UPDATE ' . $this->table;
        $sql .= $this->getJoin();
        $sql .= ' SET ';
        $columnCount = count($this->update);
        foreach ($this->update as $key => $column) {
            $sql .= $column . ' = ' . $this->getValueType($this->bindings[$key]);
            if ($key < $columnCount - 1) {
                $sql .= ', ';
            }
        }
        var_dump($this->where, $sql);
        $sql .= $this->getWhere($this);
        var_dump($this->bindings, $sql);
        return $sql;
    }

    public function delete()
    {
        $this->_method = self::DELETE;
        if ($this->_model->exists()) {
            $this->where($this->_model->getPrimaryKey(), $this->_model->getAttribute($this->_model->getPrimaryKey()));
        }
        return $this;
    }

    private function prepareDelete()
    {

        if (property_exists($this->_model, 'soft_deletes') && $this->_model->soft_deletes) {
            return $this->update(['deleted_at' => date('Y-m-d H:i:s')])->prepareUpdate();
        }

        $sql = 'DELETE FROM ' . $this->table;
        $sql .= $this->getWhere($this);
        return $sql;
    }

    public function with($relation)
    {
        if (($relational_query = $this->_model->addRelation($relation)) && (func_num_args() === 2 && func_get_args()[1] instanceof Closure )) {
            func_get_args()[1]($relational_query);
        }

        return $this;
    }

    public function startTransaction()
    {
        return Connection::query("START TRANSACTION");
    }

    public function commit()
    {
        return Connection::query("COMMIT");
    }

    public function rollback()
    {
        return Connection::query("ROLLBACK");
    }

    public function prepare($sql = null)
    {
        var_dump($this->_method);
        if (is_null($sql) && isset($this->_method)) {
            $sql = $this->{"prepare" . $this->_method}();
        }
        
        return Connection::prepare($sql, $this->bindings);
    }

    private function exec($sql = null)
    {
        var_dump($sql);
        if (is_null($sql)) {
            $sql = $this->prepare($sql);
        }
        if (is_null($sql)) {
            throw new \Exception("SQL query is null");
        }
        Connection::query($sql);

        return Connection::prop("last_result");
    }

    private function lastInsertId()
    {
        return Connection::prop("insert_id");
    }
    protected function getValueType($value)
    {
        return (gettype($value) == 'integer') ?
            '%d' : ((gettype($value) == 'double') ? '%f' : '%s');
    }

    public function __clone()
    {
        $this->bindings = [];   
    }
}
