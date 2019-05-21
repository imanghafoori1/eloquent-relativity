<?php

namespace Imanghafoori\Relativity;

use Illuminate\Support\Str;

trait DynamicRelations
{
    use BaseEloquentOverrides;

    protected static $dynamicRelations = [];

    public static function defineRelation($relationType, $relationName, $data, $constraints)
    {
        $method = function () use ($relationType, $data, $constraints) {
            $relation = $this->{$relationType} (...$data);
            foreach ($constraints as $cons) {
                $relation = $relation->{$cons[0]}(...$cons[1]);
            }

            return $relation;
        };

        static::$dynamicRelations[$relationName] = $method;
    }

    /**
     * Dynamically handle calls to the class.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        $dynamicRelation = static::$dynamicRelations[$method] ?? null;
        if (! $dynamicRelation) {
            return parent::__call($method, $parameters);
        }

        return call_user_func_array($dynamicRelation->bindTo($this, static::class), $parameters);
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
    public static function morphed_by_many($relationName, $related, $name, $table = null, $foreignPivotKey = null,
        $relatedPivotKey = null, $parentKey = null, $relatedKey = null)
    {
        return new AbstractRelation(['morphedByMany', static::class, $relationName, [$related, $name, $table, $foreignPivotKey,
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
    public static function has_many($relationName, $related, $foreignKey = null, $localKey = null)
    {
        return new AbstractRelation(['hasMany', static::class, $relationName, [$related, $foreignKey, $localKey]]);
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
    public static function has_one($relationName, $related, $foreignKey = null, $localKey = null)
    {
        return new AbstractRelation(['hasOne', static::class, $relationName, [$related, $foreignKey, $localKey]]);
    }

    /**
     * @param string $relationName
     * @param string $related
     * @param null $foreignKey
     * @param null $ownerKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public static function belongs_to($relationName, string $related, $foreignKey = null, $ownerKey = null)
    {
        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relationName).'_id';
        }

        return new AbstractRelation(['belongsTo', static::class, $relationName, [$related, $foreignKey, $ownerKey, $relationName]]);
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
    public static function belongs_to_many($relationName, $related, $table = null, $foreignPivotKey = null, $relatedPivotKey = null,
        $parentKey = null, $relatedKey = null)
    {
        $params = [$related, $table, $foreignPivotKey, $relatedPivotKey, $parentKey, $relatedKey, $relationName];

        return new AbstractRelation(['belongsToMany', static::class, $relationName, $params]);
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
    public static function morph_to_many($relationName, $related, $name, $table = null, $foreignPivotKey = null,
        $relatedPivotKey = null, $parentKey = null,
        $relatedKey = null, $inverse = false)
    {
        $params = [$related, $name, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey, $inverse, $relationName, ];

        return new AbstractRelation(['morphToMany', static::class, $relationName, $params]);
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
    public static function morph_many($relationName, $related, $name, $type = null, $id = null, $localKey = null)
    {
        $params = [$related, $name, $type, $id, $localKey];

        return new AbstractRelation(['morphMany', static::class, $relationName, $params]);
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
    public static function morph_one($relationName, $related, $name, $type = null, $id = null, $localKey = null)
    {
        $params = [$related, $name, $type, $id, $localKey];

        return new AbstractRelation(['morphOne', static::class, $relationName, $params]);
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
    public static function morph_to($relationName, $type = null, $id = null, $ownerKey = null)
    {
        $params = [$relationName, $type, $id, $ownerKey];

        return new AbstractRelation(['morphTo', static::class, $relationName, $params]);
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
    public static function has_many_through($relationName, $related, $through, $firstKey = null, $secondKey = null, $localKey = null, $secondLocalKey = null)
    {
        $params = [$related, $through, $firstKey, $secondKey, $localKey, $secondLocalKey];

        return new AbstractRelation(['hasManyThrough', static::class, $relationName, $params]);
    }

    public static function forceEagerLoading(...$relation)
    {
        static::registerModelEvent('booting', function ($model) use ($relation) {
            $model->with = $model->with + $relation;
        });
    }
}
