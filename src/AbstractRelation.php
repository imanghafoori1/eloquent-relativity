<?php

namespace Imanghafoori\Relativity;

class AbstractRelation
{
    private $relationData = null;

    private $constraints = [];

    public function __construct($data)
    {
        $this->relationData = $data;
    }

    public function __call($method, $args)
    {
        $this->constraints[] = [$method, $args];

        return $this;
    }

    public function __destruct()
    {
        $constraints = $this->constraints;

        [$relationType, $model, $relationName, $data] = $this->relationData;

        $model::defineRelation($relationName, function () use ($relationType, $data, $constraints) {
            $relation = $this->{$relationType} (...$data);
            foreach ($constraints as $cons) {
                $relation = $relation->{$cons[0]}(...$cons[1]);
            }

            return $relation;
        });
    }
}