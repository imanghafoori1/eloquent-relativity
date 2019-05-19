<?php

namespace Imanghafoori\Relativity\Tests\Normal;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = 'a3';

    public function a1()
    {
        return $this->belongsTo(User::class);
    }
}
