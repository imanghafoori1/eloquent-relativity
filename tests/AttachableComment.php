<?php

namespace Imanghafoori\Relativity\Tests;

use Illuminate\Database\Eloquent\Model;
use Imanghafoori\Relativity\DynamicRelations;

class AttachableComment extends Model
{
    use DynamicRelations;

    protected $table = 'poly_morph_comments';

    protected $guarded = [];

}