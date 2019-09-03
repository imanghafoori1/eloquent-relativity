<?php

namespace Imanghafoori\Relativity\Exceptions;

use Exception;

class UndefinedDynamicRelationException extends Exception
{
    public function __construct(string $relation)
    {
        $this->message = sprintf("Undefined dynamic relation '%s' called", $relation);
    }
}
