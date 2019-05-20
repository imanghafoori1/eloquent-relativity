<?php

namespace Imanghafoori\Relativity\Tests\Normal;

use Illuminate\Database\Eloquent\Model;

class AttachableComment extends Model
{
    protected $table = 'poly_morph_comments';

    protected $guarded = [];

    public function commentable()
    {
        return $this->morphTo('commentable', 'morphed_type', 'morphed_id');
    }
}
