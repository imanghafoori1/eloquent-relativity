<?php

namespace Imanghafoori\Relativity;

trait BaseEloquentOverrides
{
    protected function guessBelongsToManyRelation()
    {
        $debug = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 3);

        $name = $debug[1]['args'][8] ?? $debug[2]['function'];

        if ($name == 'morphedByMany') {
            $name = $debug[2]['args'][7] ?? debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 4)[3]['function'];
        }

        return $name;
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
        if (method_exists($this, $key) or static::$dynamicRelations->has($this, $key)) {
            return $this->getRelationshipFromMethod($key);
        }
    }
}
