<?php

namespace Imanghafoori\Relativity;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

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
     * Retrieve key
     *
     * @param Model $model
     * @param string $key
     *
     * @return string
     */
    public function getKey(Model $model, $key)
    {
    	return $key;
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
        return Arr::get($this->relations, $this->getKey($model, $key), $default);
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
        Arr::set($this->relations, $this->getKey($model, $key), $value);
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
        return Arr::has($this->relations, $this->getKey($model, $key));
    }

    /**
     * Remove a relation.
     *
     * @param Model $model
     * @param string $key
     */
    public function unset(Model $model, $key)
    {
        Arr::forget($this->relations, $this->getKey($model, $key));
    }
}
