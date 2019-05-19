<?php

namespace Imanghafoori\Relativity\Tests\Normal;

use Illuminate\Database\Eloquent\Model;

class A2 extends Model
{
    protected $table = 'a2';

    protected $primaryKey = 'none_id';

    public function a1()
    {
        return $this->belongsToMany(User::class, 'pivot', 'a2_id', 'a1_id', 'none_id', null);
    }
}
