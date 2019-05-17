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
    }

    public function __destruct()
    {
        return RelationDefiner::defineRelation($this->relationData, $this->constraints);
    }
}