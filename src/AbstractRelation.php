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
        [$relationType, $model, $relationName, $data] = $this->relationData;

        $model->defineRelation($relationType, $relationName, $data, $this->constraints);
    }
}
