<?php

namespace BitApps\FM\Core\Database;

use BitApps\FM\Core\Helpers\JSON;

use Closure;

use Exception;

class QueryBuilder
{
    const UPDATE = 'Update';

    const INSERT = 'Insert';

    const DELETE = 'Delete';

    const SELECT = 'Select';

    const TIME_FORMAT = 'Y-m-d H:i:s';

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

    private $_model;

    private $_from;

    private $_for;

    private $_method;

    /**
     * Constructs QueryBuilder
     *
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->_model = $model;
        $this->table  = $model->getTable();
    }

    /**
     * Do necessary when clone
     *
     * @return void
     */
    public function __clone()
    {
        $this->bindings = [];
    }

    /**
     * Sets alias for table
     *
     * @param string $_from
     *
     * @return self
     */
    public function from($_from)
    {
        $this->_from = $_from;

        return $this;
    }

    /**
     * Returns model for current query
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->_model;
    }

    /**
     * Undocumented function
     *
     * @param string $_for
     *
     * @return void
     */
    public function queryFor($_for)
    {
        $this->_for = $_for;

        return $this;
    }

    /**
     * Returns new instance
     *
     * @return self
     */
    public function newQuery()
    {
        return new QueryBuilder($this->_model);
    }

    /**
     * Add bindings for this query
     *
     * @param array $bindings
     *
     * @return void
     */
    public function addBindings($bindings)
    {
        if (\is_null($bindings)) {
            return;
        }

        if (!\is_array($bindings)) {
            $bindings = [$bindings];
        }

        $this->bindings = array_merge($this->bindings, $bindings);
    }

    /**
     * Returns bindings for this query
     *
     * @return array
     */
    public function getBindings()
    {
        return $this->bindings;
    }

    /**
     * Get all rows
     *
     * @param array $columns
     *
     * @return Model|array<int, Model>|false
     */
    public function all($columns = ['*'])
    {
        return $this->get($columns);
    }

    /**
     * Selects column for query
     *
     * @param array $columns
     *
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->select = !\is_array($columns) ? \func_get_args() : $columns;

        return $this;
    }

    /**
     * Prepare the query and execute.
     *
     * @param array $columns
     *
     * @return Model|array<int, Model>| false
     */
    public function get($columns = ['*'])
    {
        $columns       = isset($columns) && \is_array($columns) ? $columns : \func_get_args();
        if (empty($this->select) || $columns !== ['*']) {
            $this->select  = $columns;
        }

        $this->_method = self::SELECT;

        return $this->_model->getInstanceFromBuilder(
            $this->exec(),
            $this->limit == 1 ? true : false
        );
    }

    /**
     * Returns only first row from query result.
     *
     * @return Model
     */
    public function first()
    {
        return $this->take(1)->get();
    }

    /**
     * Prepare the query and execute.
     *
     * @param array $attributes
     *
     * @return Model|array<int, Model>| false
     */
    public function find($attributes)
    {
        if (!\is_array($attributes)) {
            $attributes = [$this->_model->getPrimaryKey() => $attributes];
        }

        foreach ($attributes as $key => $value) {
            $this->where($key, $value);
        }

        return $this->get();
    }

    /**
     * Returns first row
     *
     * @param array $attributes
     *
     * @return Model | false
     */
    public function findOne($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->where($key, $value);
        }

        return $this->first();
    }

    /**
     * Get processed conditions
     *
     * @param QueryBuilder $query
     * @param string       $type
     *
     * @return string|string[]|null
     */
    public function getConditions(QueryBuilder $query, $type = 'where')
    {
        return $this->processConditions($query->{$type}, $type);
    }

    /**
     * Prepare operator for where clause
     *
     * @param array $clause
     *
     * @return void
     */
    public function prepareOperatorForWhere($clause)
    {
        $sql = '';
        if (!isset($clause['column'])) {
            return $sql;
        }

        if (isset($clause['operator'])) {
            $sql .= ' ' . $clause['operator'];
        } elseif (\is_array($clause['value'])) {
            $sql .= ' IN ';
        } elseif (\is_null($clause['value'])) {
            $sql = ' IS NULL';
        } else {
            $sql .= ' = ';
        }

        return $sql;
    }

    /**
     * Set where clause
     *
     * @param array<column, ?operator, value> ...$params
     *
     * @return $this
     */
    public function where(...$params)
    {
        $this->prepareWhere($params);

        return $this;
    }

    /**
     * Set where clause with OR pipe
     *
     * @param array<column, ?operator, value> ...$params
     *
     * @return $this
     */
    public function orWhere(...$params)
    {
        $this->prepareWhere($params, 'OR');

        return $this;
    }

    /**
     * Set where clause
     *
     * @param string $sql
     * @param array  $bindings
     *
     * @return $this
     */
    public function whereRaw($sql, $bindings = [])
    {
        $this->where[] = [
            'raw'      => $sql,
            'bindings' => $bindings,
        ];

        return $this;
    }

    /**
     * Set where clause with IN operator
     *
     * @param string $column
     * @param array  $value
     *
     * @return $this
     */
    public function whereIn($column, $value)
    {
        $this->where[] = [
            'column'   => $column,
            'value'    => $value,
            'operator' => 'IN',
        ];

        return $this;
    }

    /**
     * Set where clause with null condition
     *
     * @param string $column
     *
     * @return $this
     */
    public function whereNull($column)
    {
        $this->where[] = [
            'column'   => $column,
            'operator' => 'IS NULL',
        ];

        return $this;
    }

    /**
     * Set where clause with not null condition
     *
     * @param string $column
     *
     * @return $this
     */
    public function whereNotNull($column)
    {
        $this->where[] = [
            'column'   => $column,
            'operator' => 'IS NOT NULL',
        ];

        return $this;
    }

    /**
     * Set where clause with between condition
     *
     * @param string $column
     * @param mixed  $start
     * @param mixed  $end
     *
     * @return $this
     */
    public function whereBetween($column, $start, $end)
    {
        $this->where[] = [
            'raw'      => ' (' . $column . ' BETWEEN ' . $this->getValueType($start)
                . ' AND ' . $this->getValueType($end) . ')',
            'bindings' => [$start, $end],
        ];

        return $this;
    }

    /**
     * Set where clause with between condition and or pipe
     *
     * @param string $column
     * @param mixed  $start
     * @param mixed  $end
     *
     * @return $this
     */
    public function orWhereBetween($column, $start, $end)
    {
        $this->where[] = [
            'raw'      => ' (' . $column . ' BETWEEN ' . $this->getValueType($start)
                . ' AND ' . $this->getValueType($end) . ')',
            'bindings' => [$start, $end],
            'bool'     => 'OR',
        ];

        return $this;
    }

    /**
     * Sets group by clause
     *
     * @param array|string[] $columns
     *
     * @return $this
     */
    public function groupBy($columns)
    {
        $columns       = \is_array($columns) ? $columns : \func_get_args();
        $this->groupBy = array_merge($this->groupBy, $columns);

        return $this;
    }

    /**
     * Set having clause
     *
     * @param array<column, ?operator, value> ...$params
     *
     * @return $this
     */
    public function having(...$params)
    {
        return $this->prepareHaving($params);
    }

    /**
     * Set having clause with or
     *
     * @param array<column, ?operator, value> ...$params
     *
     * @return $this
     */
    public function orHaving(...$params)
    {
        return $this->prepareHaving($params, 'OR');
    }

    /**
     * Sets join
     *
     * @param string $table
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     * @param string $type
     *
     * @return $this
     */
    public function join($table, $firstColumn, $operator = null, $secondColumn = null, $type = 'INNER')
    {
        $table    = Connection::getPrefix() . $table;
        $hasAlias = preg_split('/ as /i', $table);
        if ($hasAlias && isset($hasAlias[1])) {
            $table = $hasAlias[0];
            $alias = $hasAlias[1];
        } else {
            $alias = $table;
        }

        $on[]          = $this->prepareOn($alias, $firstColumn, $operator, $secondColumn, 'AND');
        $this->joins[] = [
            'table' => $table,
            'on'    => $on,
            'type'  => $type,
        ];

        return $this;
    }

    /**
     * Sets left join
     *
     * @param string $table
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     *
     * @return $this
     */
    public function leftJoin($table, $firstColumn, $operator = null, $secondColumn = null)
    {
        return $this->join($table, $firstColumn, $operator, $secondColumn, 'LEFT');
    }

    /**
     * Sets right join
     *
     * @param string $table
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     *
     * @return $this
     */
    public function rightJoin($table, $firstColumn, $operator = null, $secondColumn = null)
    {
        return $this->join($table, $firstColumn, $operator, $secondColumn, 'RIGHT');
    }

    /**
     * Sets right join
     *
     * @param string $table
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     *
     * @return $this
     */
    public function fullJoin($table, $firstColumn, $operator = null, $secondColumn = null)
    {
        return $this->join($table, $firstColumn, $operator, $secondColumn, 'FULL');
    }

    /**
     * Sets cross join
     *
     * @param string $table
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     *
     * @return $this
     */
    public function crossJoin($table, $firstColumn, $operator = null, $secondColumn = null)
    {
        return $this->join($table, $firstColumn, $operator, $secondColumn, 'CROSS');
    }

    /**
     * Sets on clause for join
     *
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     * @param string $bool
     *
     * @return $this
     */
    public function on($firstColumn, $operator = null, $secondColumn = null, $bool = 'AND')
    {
        $joinIndex = (\count($this->joins) - 1);
        if ($joinIndex < 0) {
            $joinIndex = 0;
        }

        $table                           = $this->joins[$joinIndex]['table'];
        $this->joins[$joinIndex]['on'][] = $this->prepareOn($table, $firstColumn, $operator, $secondColumn, $bool);

        return $this;
    }

    /**
     * Sets or on clause for join
     *
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     *
     * @return $this
     */
    public function orOn($firstColumn, $operator = null, $secondColumn = null)
    {
        return $this->on($firstColumn, $operator, $secondColumn, 'OR');
    }

    /**
     * Returns order by clause sql
     *
     * @return string
     */
    public function getOrderBy()
    {
        $sql = '';
        if (empty($this->orderBy)) {
            return $sql;
        }

        foreach ($this->orderBy as $order) {
            $sql .= $order['column'] . ' ' . $order['direction'] . ', ';
        }

        return ' ORDER BY ' . rtrim($sql, ', ');
    }

    /**
     * Sets order by
     *
     * @param string $column
     *
     * @return $this
     */
    public function orderBy($column)
    {
        $this->orderBy[] = [
            'column'    => $column,
            'direction' => 'ASC',
        ];

        return $this;
    }

    /**
     * Sets ascending order
     *
     * @return $this
     */
    public function asc()
    {
        $orderId = \count($this->orderBy);
        if ($orderId > 0) {
            $orderId = $orderId - 1;
        }

        $this->orderBy[$orderId]['direction'] = 'ASC';
        if (!isset($this->orderBy[$orderId]['column'])) {
            $this->orderBy[$orderId]['column'] = $this->_model->getPrimaryKey();
        }

        return $this;
    }

    /**
     * Sets descending order
     *
     * @return $this
     */
    public function desc()
    {
        $orderId = \count($this->orderBy);
        if ($orderId > 0) {
            $orderId = $orderId - 1;
        }

        $this->orderBy[$orderId]['direction'] = 'DESC';
        if (!isset($this->orderBy[$orderId]['column'])) {
            $this->orderBy[$orderId]['column'] = $this->_model->getPrimaryKey();
        }

        return $this;
    }

    /**
     * Runs raw query
     *
     * @param string $sql
     * @param array  $bindings
     *
     * @return mixed
     */
    public function raw($sql, $bindings = [])
    {
        return $this->exec(Connection::prepare($sql, $bindings));
    }

    /**
     * Sets limit
     *
     * @param string $count
     *
     * @return $this
     */
    public function take($count)
    {
        $this->limit = $count;

        return $this;
    }

    /**
     * Sets offset
     *
     * @param string $count
     *
     * @return $this
     */
    public function skip($count)
    {
        $this->offset = $count;

        return $this;
    }

    /**
     * Returns processed offset for query
     *
     * @return string|null
     */
    public function getOffset()
    {
        return isset($this->limit) && isset($this->offset) ? " OFFSET {$this->offset}" : '';
    }

    /**
     * Run insert query for model
     *
     * @param array $attributes
     *
     * @return Model|array<int, Model>|false
     */
    public function insert($attributes = [])
    {
        if (\is_array(reset($attributes))) {
            return $this->bulkInsert($attributes);
        }

        $this->_model->fill($attributes);
        if ($this->save()) {
            return $this->_model;
        }

        return false;
    }

    /**
     * Runs update query for model
     *
     * @param array $attributes
     *
     * @return $this
     */
    public function update($attributes = [])
    {
        $this->_method = self::UPDATE;
        $this->_model->fill($attributes);
        $this->update = $this->prepareAttributeForSaveOrUpdate(true);

        return $this;
    }

    /**
     * Runs delete query for multiple model
     *
     * @param array $ids
     *
     * @return string|bool
     */
    public function destroy($ids = [])
    {
        $this->_method = self::DELETE;
        $this->where($this->_model->getPrimaryKey(), $ids);

        return $this->exec();
    }

    /**
     * Saves the current model
     *
     * @return string|bool
     */
    public function save()
    {
        $columns = $this->prepareAttributeForSaveOrUpdate($this->_model->exists());
        $pk      = $this->_model->getPrimaryKey();
        if ($this->_model->exists()) {
            $isPkExistsInWhere = false;

            $pkValue = $this->_model->getAttribute($pk);
            foreach ($this->where as $value) {
                if ($value['column'] == $pk && $value['value'] == $pkValue && !isset($value['operator'])) {
                    $isPkExistsInWhere = true;
                }
            }

            if (!$isPkExistsInWhere) {
                $this->where($pk, $pkValue);
            }

            $this->update = $columns;

            $this->exec();

            return Connection::prop('rows_affected');
        }

        $this->insert = $columns;
        $this->exec();
        if ($insertId = $this->lastInsertId()) {
            $this->_model->setAttribute($pk, $insertId);

            return true;
        }

        return false;
    }

    /**
     * Set count for select
     *
     * @return $this
     */
    public function withCount()
    {
        $this->select[] = 'COUNT(*) as count';

        return $this;
    }

    /**
     * Get counts for current model
     *
     * @return int|null
     */
    public function count()
    {
        $this->select  = ['COUNT(*) as count'];
        $this->_method = 'Select';
        $result        = $this->exec();
        unset($this->select);

        return \is_array($result) && !empty($result[0]->count) ? $result[0]->count : null;
    }

    public function delete()
    {
        $this->_method = self::DELETE;
        if ($this->_model->exists()) {
            $this->where($this->_model->getPrimaryKey(), $this->_model->getAttribute($this->_model->getPrimaryKey()));
        }

        return $this->exec();
    }

    /**
     * Adds relation for model
     *
     * @param string|Closure $relation
     *
     * @return $this
     */
    public function with($relation)
    {
        $args            = \func_get_args();
        $relationalQuery = $this->_model->addRelation($relation);
        if ($relationalQuery && \func_num_args() === 2 && $args[1] instanceof Closure) {
            $args[1]($relationalQuery);
        }

        return $this;
    }

    /**
     * Starts transaction
     *
     * @return bool
     */
    public function startTransaction()
    {
        return Connection::query('START TRANSACTION');
    }

    /**
     * Commits current transaction
     *
     * @return bool
     */
    public function commit()
    {
        return Connection::query('COMMIT');
    }

    /**
     * Rollback previously execute query
     *
     * @return void
     */
    public function rollback()
    {
        return Connection::query('ROLLBACK');
    }

    /**
     * Sanitize query string
     *
     * @param string|null $sql
     *
     * @return string
     */
    public function prepare($sql = null)
    {
        if (\is_null($sql) && isset($this->_method)) {
            $sql = $this->{'prepare' . $this->_method}();
        }

        return empty($this->bindings) ? $sql : Connection::prepare($sql, $this->bindings);
    }

    /**
     * Process conditions
     *
     * @param array  $conditions
     * @param string $type
     *
     * @return string
     */
    protected function processConditions($conditions, $type = null)
    {
        $sql = '';
        if (\is_array($conditions) && \count($conditions) > 0) {
            foreach ($conditions as $clause) {
                if (isset($clause['bool'])) {
                    $sql .= ' ' . $clause['bool'];
                } else {
                    $sql .= ' AND';
                }

                if (isset($clause['raw'])) {
                    $sql .= ' ' . $clause['raw'];
                    $this->addBindings($clause['bindings']);

                    continue;
                }

                if (isset($clause['query']) && !\is_null($type)) {
                    $sql .= ' (' . $clause['query']->getConditions($clause['query'], $type) . ')';
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

    /**
     * Prepares conditional
     *
     * @param array  $params
     * @param string $bool
     * @param string $type
     *
     * @return array|void
     */
    protected function prepareConditional($params, $bool = 'AND', $type = 'where')
    {
        $noOfParams   = \count($params);
        $conditions   = [];
        if ($noOfParams === 0) {
            return $conditions;
        }

        if (\func_num_args() == 1 && \is_array($params[0])) {
            foreach ($params[0] as $clause) {
                if ($type === 'where') {
                    $this->prepareWhere($clause, $bool);
                }
            }

            return;
        }

        $conditions['bool'] = $bool;
        if ($params[0] instanceof Closure) {
            $nestedQuery = $this->newQuery()->queryFor($type);
            \call_user_func($params[0], $nestedQuery);
            $conditions['query'] = $nestedQuery;
            if (isset($params[1])) {
                $conditions['bool'] = $params[1];
            }
        } elseif ($noOfParams == 2) {
            $conditions['column'] = $params[0];
            $conditions['value']  = $params[1];
        } elseif ($noOfParams == 3) {
            $conditions['column']   = $params[0];
            $conditions['operator'] = $params[1];
            $conditions['value']    = $params[2];
        } elseif ($noOfParams == 4) {
            $conditions['column']                                     = $params[0];
            $conditions['operator']                                   = $params[1];
            $conditions[$type === 'where' ? 'value' : 'secondColumn'] = $params[2];
            $conditions['bool']                                       = $params[3];
        }

        return $conditions;
    }

    /**
     * Removes leading and | or
     *
     * @param string $sql
     *
     * @return string
     */
    protected function removeLeadingbool($sql)
    {
        return preg_replace('/and |or /i', '', $sql, 1);
    }

    /**
     * Returns processed sql for where clause
     *
     * @return string
     */
    protected function getWhere()
    {
        $sql = $this->getConditions($this);
        if (empty($sql)) {
            return '';
        }

        return " WHERE {$sql}";
    }

    /**
     * Prepares where conditions
     *
     * @param array  $params
     * @param string $bool
     *
     * @return void
     */
    protected function prepareWhere($params, $bool = 'AND')
    {
        $conditions = $this->prepareConditional($params, $bool);
        if (!empty($conditions)) {
            $this->where[] = $conditions;
        }
    }

    /**
     * Returns sql for group by clause
     *
     * @return string
     */
    protected function getGroupBy()
    {
        if (empty($this->groupBy)) {
            return '';
        }

        return ' GROUP BY ' . implode(',', $this->groupBy);
    }

    /**
     * Prepare having
     *
     * @param array  $params
     * @param string $bool
     *
     * @return $this
     */
    protected function prepareHaving($params, $bool = 'AND')
    {
        $conditions = $this->prepareConditional($params, $bool, 'having');
        if (!empty($conditions)) {
            $this->having[] = $conditions;
        }

        return $this;
    }

    /**
     * Return sql for having clause
     *
     * @return string
     */
    protected function getHaving()
    {
        $sql = $this->getConditions($this, 'having');
        if (empty($sql)) {
            return '';
        }

        return " HAVING {$sql}";
    }

    /**
     * Returns sql for join
     *
     * @return string
     */
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

    /**
     * Prepares on
     *
     * @param string $table
     * @param string $firstColumn
     * @param string $operator
     * @param string $secondColumn
     * @param string $bool
     * @param mixed  $column
     *
     * @return $this
     */
    protected function prepareOn($table, $column, $operator, $secondColumn, $bool = 'AND')
    {
        if (\is_null($operator) && \is_null($secondColumn)) {
            $column       = $this->_model->getTable() . '.' . $column;
            $secondColumn = $table . '.' . $column;
            $operator     = '=';
        }

        return compact('column', 'operator', 'secondColumn', 'bool');
    }

    /**
     * Returns types
     *
     * @param mixed $value
     *
     * @return string
     */
    protected function getValueType($value)
    {
        return (\gettype($value) == 'integer') ? '%d' : ((\gettype($value) == 'double') ? '%f' : '%s');
    }

    /**
     * Run bulk insert query
     *
     * @param array $attributes
     *
     * @return bool|array<int, Model>
     */
    private function bulkInsert($attributes)
    {
        $firstRow = reset($attributes);
        ksort($firstRow);
        $columns   = array_keys($firstRow);
        $createdAt = property_exists($this->_model, 'timestamps') && $this->_model->timestamps;
        if ($createdAt) {
            $columns[] = 'created_at';
        }

        $this->bindings = [];

        $sql = 'INSERT INTO ' . $this->table;
        $sql .= ' (' . implode(', ', $columns) . ')';
        $sql .= ' VALUES ';
        $values = [];
        foreach ($attributes as $row) {
            ksort($row);
            if ($createdAt) {
                $row['created_at'] = gmdate(self::TIME_FORMAT);
            }

            $rowValues = array_values($row);
            $values[]  = ' ('
                . implode(
                    ', ',
                    array_map(
                        function ($value) {
                            $this->bindings[] = $value;

                            return $this->getValueType($value);
                        },
                        $rowValues
                    )
                ) . ')';
        }

        $sql .= empty($values) ? ' default values' : ' ' . implode(',', $values);

        if ($this->raw($sql, $this->bindings) !== false) {
            $nextID       = $this->lastInsertId();
            $ids[]        = $nextID;
            $affectedRows = Connection::prop('rows_affected') - 1;
            while ($affectedRows--) {
                $ids[] = $nextID + 1;
            }

            if (
                !empty($ids)
                && ($allRows = $this->newQuery()
                    ->where($this->_model->getPrimaryKey(), $ids)
                    ->get()
                )
            ) {
                return $allRows;
            }

            return $ids;
        }

        return false;
    }

    /**
     * Table alias for select query
     *
     * @return string
     */
    private function getFrom()
    {
        return isset($this->_from) ? " {$this->_from}" : null;
    }

    /**
     * Prepare column for where clause
     *
     * @param array $clause
     *
     * @return void
     */
    private function prepareColumnForWhere($clause)
    {
        if (isset($clause['column'])) {
            return ' ' . $clause['column'];
        }
    }

    /**
     * Prepare value for where clause
     *
     * @param array $clause
     * @param self  $query
     *
     * @return string
     */
    private function prepareValueForWhere($clause, self $query)
    {
        $sql = '';
        if (isset($clause['secondColumn'])) {
            return ' ' . $clause['secondColumn'];
        }

        if (!isset($clause['value'])) {
            return $sql;
        }

        if (\is_array($clause['value'])) {
            $sql .= ' (';
            foreach ($clause['value'] as $value) {
                $sql .= $this->getValueType($value) . ',';
                $query->addBindings($value);
            }

            $sql = rtrim($sql, ',') . ')';
        } elseif (isset($clause['operator']) && strpos($clause['operator'], 'IS') !== false) {
            $sql .= ' ' . $clause['value'];
        } elseif (isset($clause['operator']) && strtoupper($clause['operator'] === 'LIKE')) {
            $sql .= ' %s';
            $query->addBindings($clause['value']);
        } elseif (!\is_null($clause['value'])) {
            $sql .= ' ' . $query->getValueType($clause['value']);
            $query->addBindings($clause['value']);
        }

        return $sql;
    }

    /**
     * Returns limit part for query
     *
     * @return string|null
     */
    private function getLimit()
    {
        return isset($this->limit) ? " LIMIT {$this->limit}" : '';
    }

    /**
     * Prepares columns and value
     *
     * @param bool $isUpdate
     *
     * @return array
     */
    private function prepareAttributeForSaveOrUpdate($isUpdate = false)
    {
        if ($isUpdate) {
            $columnsToPrepare = array_keys($this->_model->getDirtyAttributes());
            $this->bindings   = [];
        } else {
            $columnsToPrepare = array_keys($this->_model->getAttributes());
        }

        if (property_exists($this->_model, 'timestamps') && $this->_model->timestamps) {
            if (!$isUpdate) {
                $this->_model->setAttribute('created_at', gmdate(self::TIME_FORMAT));
                $columnsToPrepare[] = 'created_at';
            }

            $this->_model->setAttribute('updated_at', gmdate(self::TIME_FORMAT));
            $columnsToPrepare[] = 'updated_at';
        }

        $attributes = $this->_model->getAttributes();
        foreach ($columnsToPrepare as $key => $column) {
            if (isset($attributes[$column])) {
                $this->bindings[] = JSON::maybeEncode($this->_model->{$column});
            } else {
                $this->bindings[] = '';
            }
        }

        // if (isset($attributes[$this->_model->getPrimaryKey()])
        // && !in_array($this->_model->getPrimaryKey(), $columnsToPrepare)) {
        //     $columnsToPrepare[] = $this->_model->getPrimaryKey();
        //     $this->bindings[] = $attributes[$this->_model->getPrimaryKey()];
        // }
        $this->_method = $isUpdate ? self::UPDATE : self::INSERT;

        return $columnsToPrepare;
    }

    /**
     * Prepares select statement
     *
     * @return string
     */
    private function prepareSelect()
    {
        $this->bindings = [];
        $sql            = 'SELECT ' . implode(',', $this->select) . ' FROM ' . $this->table;
        $sql           .= $this->getFrom();
        $sql           .= $this->getJoin();
        $sql           .= $this->getWhere($this);
        $sql           .= $this->getGroupBy();
        $sql           .= $this->getHaving();
        $sql           .= $this->getOrderBy();
        $sql           .= $this->getLimit();
        $sql           .= $this->getOffset();

        return trim($sql);
    }

    /**
     * Prepares insert statement
     *
     * @return string
     */
    private function prepareInsert()
    {
        $sql  = 'INSERT INTO ' . $this->table;
        $sql .= ' (' . implode(', ', $this->insert) . ')';
        $sql .= ' VALUES ('
            . implode(
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

    /**
     * Prepares update statement
     *
     * @return string
     */
    private function prepareUpdate()
    {
        $sql = 'UPDATE ' . $this->table;
        $sql .= $this->getJoin();
        $sql .= ' SET ';
        $columnCount = \count($this->update);
        foreach ($this->update as $key => $column) {
            $sql .= $column . ' = ' . $this->getValueType($this->bindings[$key]);
            if ($key < $columnCount - 1) {
                $sql .= ', ';
            }
        }

        $sql .= $this->getWhere($this);

        return $sql;
    }

    /**
     * Prepares delete statement
     *
     * @return string
     */
    private function prepareDelete()
    {
        if (property_exists($this->_model, 'soft_deletes') && $this->_model->soft_deletes) {
            return $this->update(['deleted_at' => gmdate(self::TIME_FORMAT)])->prepareUpdate();
        }

        $sql = 'DELETE FROM ' . $this->table;
        $sql .= $this->getWhere($this);

        return $sql;
    }

    /**
     * Executes sql query
     *
     * @param string $sql
     *
     * @return array|object|string|false
     */
    private function exec($sql = null)
    {
        if (\is_null($sql)) {
            $sql = $this->prepare($sql);
        }

        if (\is_null($sql)) {
            throw new Exception('SQL query is null');
        }

        Connection::query($sql);

        if (!empty(Connection::prop('last_error'))) {
            return false;
        }

        return Connection::prop('last_result');
    }

    /**
     * Returns last id
     *
     * @return string
     */
    private function lastInsertId()
    {
        return Connection::prop('insert_id');
    }
}
