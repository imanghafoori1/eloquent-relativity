<?php

namespace Imanghafoori\Relativity;

class RelationDefiner
{
    public static function defineRelation($relationData, $constraints = []): void
    {
        [$relationType, $model, $relationName, $data] = $relationData;

        $model::defineRelation($relationName, function () use ($relationType, $data, $constraints) {
            $relation = $this->{$relationType} (...$data);
            foreach ($constraints as $cons) {
                $relation = $relation->{$cons[0]}(...$cons[1]);
            }

            return $relation;
        });
    }
}