<?php

namespace Imanghafoori\Relativity;

use Closure;
use Illuminate\Support\Str;

trait DynamicRelations
{
    /**
     * Define a polymorphic many-to-many relationship.
     *
     * @param  string  $related
     * @param  string  $name
     * @param  string  $table
     * @param  string  $foreignPivotKey
     * @param  string  $relatedPivotKey
     * @param  string  $parentKey
     * @param  string  $relatedKey
     * @param  bool  $inverse
     * @param  string  $caller
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function morphToMany($related, $name, $table = null, $foreignPivotKey = null,
        $relatedPivotKey = null, $parentKey = null,
        $relatedKey = null, $inverse = false, $caller = null)
    {
        $caller = $caller ?: $this->guessBelongsToManyRelation();

        // First, we will need to determine the foreign key and "other key" for the
        // relationship. Once we have determined the keys we will make the query
        // instances, as well as the relationship instances we need for these.
        $instance = $this->newRelatedInstance($related);

        $foreignPivotKey = $foreignPivotKey ?: $name.'_id';

        $relatedPivotKey = $relatedPivotKey ?: $instance->getForeignKey();

        // Now we're ready to create a new query builder for this related model and
        // the relationship instances for this relation. This relations will set
        // appropriate query constraints then entirely manages the hydrations.
        $table = $table ?: Str::plural($name);

        return $this->newMorphToMany(
            $instance->newQuery(), $this, $name, $table,
            $foreignPivotKey, $relatedPivotKey, $parentKey ?: $this->getKeyName(),
            $relatedKey ?: $instance->getKeyName(), $caller, $inverse
        );
    }

    protected static $macros = [];

    public function getRelationValue($key)
    {
        // If the key already exists in the relationships array, it just means the
        // relationship has already been loaded, so we'll just return it out of
        // here because there is no need to query within the relations twice.
        if ($this->relationLoaded($key)) {
            return $this->relations[$key];
        }

        // If the "attribute" exists as a method on the model, we will just assume
        // it is a relationship and will load and return results from the query
        // and hydrate the relationship's value on the "relationships" array.
        if (method_exists($this, $key) or isset(static::$macros[$key])) {
            return $this->getRelationshipFromMethod($key);
        }
    }

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