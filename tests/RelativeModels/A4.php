<?php

namespace Imanghafoori\Relativity\Tests\RelativeModels;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class A4 extends Model
{
    use DynamicRelations;

    protected $table = 'a4';
}
