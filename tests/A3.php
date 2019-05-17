<?php

namespace Imanghafoori\Relativity\Tests;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class A3 extends Model
{
    use DynamicRelations;

    protected $table = 'a3';
}