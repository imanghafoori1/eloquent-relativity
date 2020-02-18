<?php

namespace Imanghafoori\Relativity;

trait DynamicRelations
{
    use BaseEloquentOverrides;

    protected static $methodAllowed = [
        'getDynamicRelations',
        'hasDynamicRelation',
        'removeRelation',
        'defineRelation',
        'morphed_by_many',
        'has_many',
        'has_one',
        'belongs_to',
        'belongs_to_many',
        'morph_to_many',
        'morph_many',
        'morph_one',
        'morph_to',
        'has_many_through'
    ];

    /**
     * @var RelationStore
     */
    protected static $relationStore;

    /**
     * retrieve a new instance of the store
     *
     * @return RelationStore
     */
    public function newRelationStoreClass(): RelationStore
    {
        return new RelationStore();
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
        if (empty(static::$relationStore)) {
            static::$relationStore = $this->newRelationStoreClass();
        }

        // Handle internal calls
        if (in_array($method, static::$methodAllowed)) {
            $manager = new RelationManager(static::$relationStore);

            return $manager->$method(...array_merge([$this], $parameters));
        }

        // Handle relationships
        $dynamicRelation = static::$relationStore->get($this, $method);

        if ($dynamicRelation) {
            return call_user_func_array($dynamicRelation->bindTo($this, static::class), $parameters);
        }
        
        return parent::__call($method, $parameters);
    }

    /**
     * Convert static call to
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     *
     * @throws \BadMethodCallException
     */
    public static function __callStatic($method, $parameters)
    {
        if (in_array($method, static::$methodAllowed)) {
            $entity = new static;
            return $entity->$method(...$parameters);
        } else {
            return parent::__callStatic($method, $parameters);
        }
    }
  

    public static function forceEagerLoading(...$relation)
    {
        static::registerModelEvent('booting', function ($model) use ($relation) {
            $model->with = $model->with + $relation;
        });
    }
}
