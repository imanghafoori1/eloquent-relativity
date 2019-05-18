<?php

namespace Imanghafoori\Relativity\Tests\Normal;

use Illuminate\Database\Eloquent\Model;

class A1 extends Model
{
    protected $table = 'a1';

    public function a3()
    {
        return $this->hasMany(A3::class);
    }

    public function a13()
    {
        return $this->hasMany(A3::class, 'a1_d_id');
    }

    public function a13_ordered()
    {
        return $this->hasMany(A3::class, 'a1_d_id')->orderByDesc('id');
    }

    public function a2()
    {
        return $this->belongsToMany(A2::class, 'pivot', 'a1_id', 'a2_id', null, 'none_id');
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commented', 'morphed_type', 'morphed_id');
    }

    public function a4()
    {
        return $this->hasOne(A4::class);
    }
}