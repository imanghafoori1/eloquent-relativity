<?php

namespace Imanghafoori\Relativity\Tests\Normal;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'a1';

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function comments2()
    {
        return $this->hasMany(Comment::class, 'a1_d_id');
    }

    public function commentsSorted()
    {
        return $this->hasMany(Comment::class, 'a1_d_id')->orderByDesc('id');
    }

    public function a2()
    {
        return $this->belongsToMany(A2::class, 'pivot', 'a1_id', 'a2_id', null, 'none_id');
    }

    public function poly_comments()
    {
        return $this->morphMany(AttachableComment::class, 'commented', 'morphed_type', 'morphed_id');
    }

    public function a4()
    {
        return $this->hasOne(A4::class, 'a1_id');
    }
}
