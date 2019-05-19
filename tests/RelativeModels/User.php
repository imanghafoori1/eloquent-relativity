<?php

namespace Imanghafoori\Relativity\Tests\RelativeModels;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class User extends Model
{
    use DynamicRelations;

    protected $table = 'a1';
}
