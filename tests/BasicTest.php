<?php

namespace Imanghafoori\Relativity\Tests;

use Imanghafoori\Relativity\Tests\RelativeModels\User;
use Imanghafoori\Relativity\Tests\RelativeModels\Comment;
use Imanghafoori\Relativity\Exceptions\UndefinedDynamicRelationException;

class BasicTest extends TestCase
{
    public function test_remove_relation()
    {
        User::has_many('comments', Comment::class);
        $this->assertTrue((new User)->hasDynamicRelation('comments'));
        $this->assertEquals(1, count((new User)->getDynamicRelations()));

        User::removeRelation('comments');
        $this->assertFalse((new User)->hasDynamicRelation('comments'));
        $this->assertEquals(0, count((new User)->getDynamicRelations()));
    }

    public function test_exception_undefined_relation()
    {
        $this->expectException(UndefinedDynamicRelationException::class);
        User::removeRelation('comments');
    }
}
