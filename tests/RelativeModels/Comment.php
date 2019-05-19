<?php

namespace Imanghafoori\Relativity\Tests\RelativeModels;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class Comment extends Model
{
    use DynamicRelations;

    protected $table = 'a3';
    protected $guarded = [];
}
