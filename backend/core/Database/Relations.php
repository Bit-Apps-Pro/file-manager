<?php

/**
 * Class For Database Relations.
 */

namespace BitApps\FM\Core\Database;

if (!\defined('ABSPATH')) {
    exit;
}

trait Relations
{
    private $_relations = [];

    private $_relatedData = [];

    private $_relationKeys = [];

    /**
     * Undocumented function.
     *
     * @param string     $table
     * @param string     $key
     * @param string     $type
     * @param mixed      $model
     * @param null|mixed $foreignKey
     * @param null|mixed $localKey
     *
     * @return QueryBuilder
     */
    public function belongsTo($model, $foreignKey = null, $localKey = null)
    {
        $relationKeys = $this->getRelationKeys($foreignKey, $localKey);
        $foreignKey   = $relationKeys[0];
        $localKey     = $relationKeys[1];

        return $this->newBelongsTo($model, $foreignKey, $localKey);
    }

    public function newBelongsTo($model, $foreignKey = null, $localKey = null)
    {
        $model = new $model();
        $model->setRelateAs('oneToOne');
        $model->_relationKeys['oneToOne'] = [
            'foreignKey' => $foreignKey,
            'localKey'   => $localKey,
        ];

        return $model->newQuery();
    }

    public function hasOne($model, $foreignKey = null, $localKey = null)
    {
        return $this->belongsTo($model, $foreignKey, $localKey);
    }

    public function hasMany($model, $foreignKey = null, $localKey = null)
    {
        $relationKeys = $this->getRelationKeys($foreignKey, $localKey);
        $foreignKey   = $relationKeys[0];
        $localKey     = $relationKeys[1];

        return $this->newHasMany($model, $foreignKey, $localKey);
    }

    public function newHasMany($model, $foreignKey = null, $localKey = null)
    {
        $model = new $model();
        $model->setRelateAs('hasMany');
        $model->_relationKeys['hasMany'] = [
            'foreignKey' => $foreignKey,
            'localKey'   => $localKey,
        ];

        return $model->newQuery();
    }

    public function belongsToMany($model, $foreignKey = null, $localKey = null)
    {
        $relationKeys = $this->getRelationKeys($foreignKey, $localKey);
        $foreignKey   = $relationKeys[0];
        $localKey     = $relationKeys[1];

        return $this->newBelongsToMany($model, $foreignKey, $localKey);
    }

    public function newBelongsToMany($model, $foreignKey = null, $localKey = null)
    {
        $model = new $model();
        $model->setRelateAs('belongsToMany');
        $model->_relationKeys['belongsToMany'] = [
            'foreignKey' => $foreignKey,
            'localKey'   => $localKey,
        ];

        return $model->newQuery();
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
    }

    public function getRelationalKeys()
    {
        return $this->_relationKeys;
    }

    private function getRelationKeys($foreignKey, $localKey)
    {
        if (!$foreignKey) {
            $foreignKey = $this->getForeignKey();
        }

        if (!$localKey) {
            $localKey = $this->getPrimaryKey();
        }

        return [$foreignKey, $localKey];
    }

    private function retrieveRelateData(QueryBuilder $query)
    {
        $relations = $this->getRelations();

        if (\count($relations) > 0) {
            foreach ($relations as $relationName => $relationQuery) {
                $parentQuery = clone $query;
                $relationKey = $relationQuery->getModel()
                    ->getRelationalKeys()[$relationQuery->getModel()->getRelateAs()];

                $relationQuery->whereRaw(
                    $relationKey['foreignKey']
                        . ' IN ( SELECT * FROM ('
                        . $parentQuery->select($relationKey['localKey'])->prepare()
                        . ') AS subquery )'
                );
                if ($relationQuery->getModel()->getRelateAs() == 'oneToOne') {
                    $relatedModels = $relationQuery->first();
                } else {
                    $relatedModels = $relationQuery->get();
                }

                if ($relatedModels) {
                    if ($relatedModels instanceof Model) {
                        $this->_relatedData[$relationName][$relatedModels->getAttribute($relationKey['foreignKey'])]
                            = $relatedModels;
                    } else {
                        foreach ($relatedModels as $relatedModel) {
                            $this->_relatedData[$relationName][$relatedModel->getAttribute($relationKey['foreignKey'])][]
                                = $relatedModel;
                        }
                    }
                }
            }
        }
    }

    private function setRelatedData(Model $model)
    {
        $relations = $this->getRelations();
        if (\count($relations) > 0) {
            foreach ($relations as $relationName => $relationQuery) {
                $relationKey = $relationQuery->getModel()
                    ->getRelationalKeys()[$relationQuery->getModel()->getRelateAs()];

                $data = isset(
                    $this->_relatedData[$relationName][$model->getAttribute($relationKey['localKey'])]
                ) ? $this->_relatedData[$relationName][$model->getAttribute($relationKey['localKey'])] : null;
                $model->setAttribute($relationName, $data);
            }
        }
    }
}
