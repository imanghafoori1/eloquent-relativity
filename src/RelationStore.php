<?php

namespace Imanghafoori\Relativity;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class RelationStore
{
    /**
     * List of all relations
     *
     * @var array
     */
    public $relations = [];

    /**
     * Retrieve all relations.
     *
     * @param Model $model
     *
     * @return array
     */
    public function all(Model $model)
    {
        return $this->relations;
    }

    /**
     * Retrieve a relation.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(Model $model, $key, $default = null)
    {
        return $this->relations[$key] ?? $default;
    }

    /**
     * Set a relation.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $value
     */
    public function set(Model $model, $key, $value)
    {
        $this->relations[$key] = $value;
    }

    /**
     * Retrieve a relation.
     *
     * @param Model $model
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function has(Model $model, $key)
    {
        return isset($this->relations[$key]);
    }

    /**
     * Remove a relation.
     *
     * @param Model $model
     * @param string $key
     */
    public function unset(Model $model, $key)
    {
        unset($this->relations[$key]);
    }
}
