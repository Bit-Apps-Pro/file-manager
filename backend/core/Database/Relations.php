<?php
/**
 * Class For Database Relations
 *
 * @category Database
 *
 * @author  Bit Code Developer <developer@bitcode.pro>
 */

namespace BitApps\FM\Core\Database;

if (! defined('ABSPATH')) {
    exit;
}

trait Relations
{
    private $_relations = [];
    private $_related_data = [];
    private  $_relation_keys = [];

    /**
     * Undocumented function
     *
     * @param string $table
     * @param string $key
     * @param string $type
     *
     * @return QueryBuilder
     */
    public function belongsTo($model, $foreignKey = null, $localKey = null)
    {
        [$foreignKey, $localKey] = $this->getRelationKeys($foreignKey, $localKey);
        return $this->newBelongsTo($model, $foreignKey, $localKey);
    }

    public function newBelongsTo($model, $foreignKey = null, $localKey = null)
    {
        $model = new $model();
        $model->setRelateAs('oneToOne');
        $model->_relation_keys['oneToOne'] = [
            'foreignKey' => $foreignKey,
            'localKey' => $localKey,
        ];
        return $model->newQuery();
    }

    public function hasOne($model, $foreignKey = null, $localKey = null)
    {
        return $this->belongsTo($model, $foreignKey, $localKey);
    }

    public function hasMany($model, $foreignKey = null, $localKey = null)
    {
        [$foreignKey, $localKey] = $this->getRelationKeys($foreignKey, $localKey);
        return $this->newHasMany($model, $foreignKey, $localKey);
    }
    
    public function newHasMany($model, $foreignKey = null, $localKey = null)
    {
        $model = new $model();
        $model->setRelateAs('hasMany');
        $model->_relation_keys['hasMany'] = [
            'foreignKey' => $foreignKey,
            'localKey' => $localKey,
        ];
        return $model->newQuery();
    }
    public function belongsToMany($model, $foreignKey = null, $localKey = null)
    {
        [$foreignKey, $localKey] = $this->getRelationKeys($foreignKey, $localKey);
        return $this->newBelongsToMany($model, $foreignKey, $localKey);
    }

    public function newBelongsToMany($model, $foreignKey = null, $localKey = null)
    {
        $model = new $model();
        $model->setRelateAs('belongsToMany');
        $model->_relation_keys['belongsToMany'] = [
            'foreignKey' => $foreignKey,
            'localKey' => $localKey,
        ];
        return $model->newQuery();
    }

    private function getRelationKeys($foreignKey, $localKey)
    {
        if (! $foreignKey) {
            $foreignKey = $this->getForeignKey();
        }
        if (! $localKey) {
            $localKey = $this->getPrimaryKey();
        }
        return [$foreignKey, $localKey];
    }

    public function getRelations()
    {
        return $this->_relations;
    }

    public function addRelation($relation)
    {
        if (method_exists($this, $relation)) {
            $this->_relations[$relation] = $this->{$relation}();
            return $this->_relations[$relation];
        }
        return null;
    }

    private function retrieveRelateData(QueryBuilder $query)
    {
        $relations = $this->getRelations();

        if (count($relations) > 0) {
            foreach ($relations as $relation_name => $relation_query) {
                $parent_query = clone $query;
                $relation_key = $relation_query->getModel()->getRelationalKeys()[$relation_query->getModel()->getRelateAs()];
                $relation_query->whereRaw($relation_key['foreignKey'] . " IN ( SELECT * FROM (". $parent_query->select($relation_key['localKey'])->prepare()  .") AS subquery )");
                if ($relation_query->getModel()->getRelateAs() == 'oneToOne') {
                    $related_models = $relation_query->first();
                } else {
                    $related_models = $relation_query->get();
                }
                if ($related_models) {
                    if ($related_models instanceof Model) {
                        $this->_related_data[$relation_name][$related_models->getAttribute($relation_key['foreignKey'])] = $related_models;
                    } else {
                        foreach ($related_models as $related_model) {
                            $this->_related_data[$relation_name][$related_model->getAttribute($relation_key['foreignKey'])][] = $related_model;
                        }
                    }
                }
            }
        }
    }

    private function setRelatedData(Model $model)
    {
        $relations = $this->getRelations();
        if (count($relations) > 0) {
            foreach ($relations as $relation_name => $relation_query) {
                $relation_key = $relation_query->getModel()->getRelationalKeys()[$relation_query->getModel()->getRelateAs()];
                $data = isset($this->_related_data[$relation_name][$model->getAttribute($relation_key['localKey'])]) ? $this->_related_data[$relation_name][$model->getAttribute($relation_key['localKey'])] : null;
                $model->setAttribute($relation_name, $data);
            }
        }
    }

    public function getRelationalKeys()
    {
        return $this->_relation_keys;
    }
}