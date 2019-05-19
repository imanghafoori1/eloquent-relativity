<?php

namespace Imanghafoori\Relativity\Tests;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class Comment extends Model
{
    use DynamicRelations;

    protected $table = 'a3';
    protected $guarded = [];
}