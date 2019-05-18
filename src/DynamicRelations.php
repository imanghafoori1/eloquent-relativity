<?php

namespace Imanghafoori\Relativity;

use Closure;
use Illuminate\Support\Str;

trait DynamicRelations
{
    use BaseEloquentOverrides;

    protected static $macros = [];

    public static function macro($name, $macro)
    {
        static::$macros[$name] = $macro;
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
        $macro = static::$macros[$method] ?? null;
        if (! $macro) {
            return parent::__call($method, $parameters);
        }

        if ($macro instanceof Closure) {
            $macro = $macro->bindTo($this, static::class);
        }

        return call_user_func_array($macro, $parameters);
    }

    /**
     * @param string $related
     * @param $relationName
     * @param null $foreignKey
     * @param null $localKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public static function has_many($relationName = null, string $related, $foreignKey = null, $localKey = null)
    {
        return new AbstractRelation(['hasMany', static::class, $relationName, [$related, $foreignKey, $localKey]]);
    }

    public static function has_one($relationName = null, $related, $foreignKey = null, $localKey = null)
    {
        return new AbstractRelation(['hasOne', static::class, $relationName, [$related, $foreignKey, $localKey]]);
    }

    /**
     * @param string $related
     * @param $relationName
     * @param null $foreignKey
     * @param null $ownerKey
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public static function belongs_to($relationName = null, string $related, $foreignKey = null, $ownerKey = null)
    {
        if (is_null($foreignKey)) {
            $foreignKey = Str::snake($relationName).'_id';
        }

        return new AbstractRelation(['belongsTo', static::class, $relationName, [$related, $foreignKey, $ownerKey, $relationName]]);
    }

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morph_to_many($relationName, $related, $name, $table = null, $foreignPivotKey = null,
        $relatedPivotKey = null, $parentKey = null, $relatedKey = null)
    {
        $params = [$related, $name, $table, $foreignPivotKey,
            $relatedPivotKey, $parentKey, $relatedKey, $relationName];

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
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public static function morph_many($relationName, $related, $name, $type = null, $id = null, $localKey = null)
    {
        $params = [$related, $name, $type, $id, $localKey];

        return new AbstractRelation(['morphMany', static::class, $relationName, $params]);
    }

    public static function morph_one($relationName, $related, $name, $type = null, $id = null, $localKey = null)
    {
        $params = [$related, $name, $type, $id, $localKey];
        return new AbstractRelation(['morphOne', static::class, $relationName, $params]);
    }

    public static function morph_to($relationName, $type = null, $id = null, $ownerKey = null)
    {
        $params = [$relationName, $type, $id, $ownerKey];

        return new AbstractRelation(['morphTo', static::class, $relationName, $params]);
    }

    public static function forceEagerLoading(...$relation)
    {
        static::registerModelEvent('booting', function ($model) use ($relation) {
            $model->with = $model->with + $relation;
        });
    }
}