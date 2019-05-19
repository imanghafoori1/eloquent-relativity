<?php

namespace Imanghafoori\Relativity\Tests;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class User extends Model
{
    use DynamicRelations;

    protected $table = 'a1';
}