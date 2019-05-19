<?php

namespace Imanghafoori\Relativity\Tests\RelativeModels;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class Tag extends Model
{
    use DynamicRelations;

    protected $guarded = [];
}
