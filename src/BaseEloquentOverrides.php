<?php

namespace Imanghafoori\Relativity;

trait BaseEloquentOverrides
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

}