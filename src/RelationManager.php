<?php

namespace Imanghafoori\Relativity;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class RelationManager
{
    /**
     * @var RelationStore
     */
    protected $store;

    /**
     * Create a new instance
     */
    public function __construct(RelationStore $store)
    {
        $this->store = $store;
    }

    /**
     * @return RelationStore
     */
    public function getStore()
    {
        return $this->store;
    }

    /**
     * Retrieve all relations
     *
     * @return array
     */
    public function getDynamicRelations(Model $model)
    {
        return $this->store->all($model);
    }

    /**
     * Has the model the relation
     *
     * @param string
     *
     * @return bool
     */
    public function hasDynamicRelation(Model $model, string $relation)
    {
        return $this->store->has($model, $relation);
    }

    /**
     * Remove a relation from the model
     *
     * @param string $relation
     */
    public function removeRelation(Model $model, string $relation)
    {
        if (!$this->store->has($model, $relation)) {
            throw new Exceptions\UndefinedDynamicRelationException($relation);
        }

        $this->store->unset($model, $relation);
    }

    /**
     * Define a new relation
     *
     * @param string $relationType
     * @param string $relationName
     * @param array $data
     * @param array $constraints
     */
    public function defineRelation(Model $model, $relationType, $relationName, $data, $constraints)
    {
        $method = function () use ($relationType, $data, $constraints) {
            $relation = $this->{$relationType} (...$data);
            foreach ($constraints as $cons) {
                $relation = $relation->{$cons[0]}(...$cons[1]);
            }

            return $relation;
        };

        $this->store->set($model, $relationName, $method);
    }
    /**
     * Define a polymorphic, inverse many-to-many relationship.
     *
     * @param  string  $relationName
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphed_by_many(
        Model $model,
        $relationName,
        $related,
        $name,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null
    ) {
        return new AbstractRelation(['morphedByMany', $model, $relationName, [$related, $name, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey, $relationName, ]]);
    }

    /**
     * @param string $relationName
     * @param string $related
     * @param null $foreignKey
     * @param null $localKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function has_many(Model $model, $relationName, $related, $foreignKey = null, $localKey = null)
    {
        return new AbstractRelation(['hasMany', $model, $relationName, [$related, $foreignKey, $localKey]]);
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param  string  $relationName
     * @param  string  $related
     * @param  string  $foreignKey
     * @param  string  $localKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function has_one(Model $model, $relationName, $related, $foreignKey = null, $localKey = null)
    {
        return new AbstractRelation(['hasOne', $model, $relationName, [$related, $foreignKey, $localKey]]);
    }

    /**
     * @param string $relationName
     * @param string $related
     * @param null $foreignKey
     * @param null $ownerKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function belongs_to(Model $model, $relationName, $related, $foreignKey = null, $ownerKey = null)
    {
        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relationName).'_id';
        }

        return new AbstractRelation(['belongsTo', $model, $relationName, [$related, $foreignKey, $ownerKey, $relationName]]);
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param  string  $relationName
     * @param  string  $related
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function belongs_to_many(
        Model $model,
        $relationName,
        $related,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null
    ) {
        $params = [$related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName];

        return new AbstractRelation(['belongsToMany', $model, $relationName, $params]);
    }

    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @param  string  $relationName
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  bool  $inverse
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morph_to_many(
        Model $model,
        $relationName,
        $related,
        $name,
        $table = null,
        $foreignPivotKey = null,
        $relatedPivotKey = null,
        $parentKey = null,
        $relatedKey = null,
        $inverse = false
    ) {
        $params = [$related, $name, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey, $inverse, $relationName, ];

        return new AbstractRelation(['morphToMany', $model, $relationName, $params]);
    }

    /**
     * Define a polymorphic one-to-many relationship.
     *
     * @param  string  $relationName
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function morph_many(Model $model, $relationName, $related, $name, $type = null, $id = null, $localKey = null)
    {
        $params = [$related, $name, $type, $id, $localKey];

        return new AbstractRelation(['morphMany', $model, $relationName, $params]);
    }

    /**
     * Define a polymorphic one-to-one relationship.
     *
     * @param  string  $relationName
     * @param  string  $related
     * @param  string  $name
     * @param  string  $type
     * @param  string  $id
     * @param  string  $localKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function morph_one(Model $model, $relationName, $related, $name, $type = null, $id = null, $localKey = null)
    {
        $params = [$related, $name, $type, $id, $localKey];

        return new AbstractRelation(['morphOne', $model, $relationName, $params]);
    }

    /**
     * Define a polymorphic, inverse one-to-one or many relationship.
     *
     * @param  string  $relationName
     * @param  string  $type
     * @param  string  $id
     * @param  string  $ownerKey
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function morph_to(Model $model, $relationName, $type = null, $id = null, $ownerKey = null)
    {
        $params = [$relationName, $type, $id, $ownerKey];

        return new AbstractRelation(['morphTo', $model, $relationName, $params]);
    }

    /**
     * Define a has-many-through relationship.
     *
     * @param  string  $relationName
     * @param  string  $related
     * @param  string  $through
     * @param  string|null  $firstKey
     * @param  string|null  $secondKey
     * @param  string|null  $localKey
     * @param  string|null  $secondLocalKey
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function has_many_through(Model $model, $relationName, $related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null)
    {
        $params = [$related, $through, $firstKey, $secondKey, $localKey, $secondLocalKey];

        return new AbstractRelation(['hasManyThrough', $model, $relationName, $params]);
    }
}
