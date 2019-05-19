<?php

namespace Imanghafoori\Relativity\Tests\RelativeModels;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class A2 extends Model
{
    use DynamicRelations;

    protected $table = 'a2';

    protected $primaryKey = 'none_id';
}