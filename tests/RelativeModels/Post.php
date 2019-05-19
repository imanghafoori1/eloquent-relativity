<?php

namespace Imanghafoori\Relativity\Tests\RelativeModels;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class Post extends Model
{
    use DynamicRelations;
}
