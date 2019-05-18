<?php

namespace Imanghafoori\Relativity\Tests\Normal;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $table = 'poly_morph_comments';
}