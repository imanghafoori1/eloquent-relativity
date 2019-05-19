<?php

namespace Imanghafoori\Relativity\Tests\Normal;

use Illuminate\Database\Eloquent\Model;

class A4 extends Model
{
    protected $table = 'a4';

    public function a1()
    {
        return $this->belongsTo(User::class);
    }
}
