<?php

namespace Imanghafoori\Relativity\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Imanghafoori\Relativity\Tests\Normal\User as UserN;
use Imanghafoori\Relativity\Tests\Normal\A2 as A2N;
use Imanghafoori\Relativity\Tests\Normal\Comment as A3N;
use Imanghafoori\Relativity\Tests\Normal\Post as NormalPost;
use Imanghafoori\Relativity\Tests\Normal\Tag as NormalTag;
use Imanghafoori\Relativity\Tests\Normal\Video as NormalVideo;
use Imanghafoori\Relativity\Tests\Normal\AttachableComment as NAttachableComment;
use Imanghafoori\Relativity\Tests\Normal\NUser;

class SampleTest extends TestCase
{
    public function test_one_to_many_relation()
    {
        $this->migrateAndSeed();

        User::has_many('comments', Comment::class);
        User::has_many('comments2', Comment::class, 'a1_d_id');
        User::has_many('commentsSorted', Comment::class, 'a1_d_id')->orderByDesc('id');
        Comment::belongs_to('a1', User::class, 'a1_id');

        $this->assertEquals(UserN::find(1)->comments->count(), User::find(1)->comments->count());
        $this->assertEquals(UserN::find(1)->comments()->count(), User::find(1)->comments()->count());
        $this->assertEquals(get_class(UserN::find(1)->comments()), get_class(User::find(1)->comments()));

        $this->assertEquals(UserN::find(1)->comments->count(), User::find(1)->comments->count());
        $this->assertEquals(UserN::find(1)->comments()->count(), User::find(1)->comments()->count());
        $this->assertEquals(get_class(UserN::find(1)->comments2()), get_class(User::find(1)->comments2()));

        $this->assertEquals(UserN::find(1)->comments2()->toSql(), User::find(1)->comments2()->toSql() );
        $this->assertEquals(get_class(UserN::find(1)->comments2), get_class(User::find(1)->comments2));

        $this->assertEquals(User::find(1)->commentsSorted->first()->id, UserN::find(1)->commentsSorted->first()->id);
        $this->assertEquals(User::find(1)->commentsSorted()->toSql(), UserN::find(1)->commentsSorted()->toSql());
    }

    public function test_many_to_many_relation()
    {
        $this->migrateAndSeed();

        User::belongs_to_many('a2', A2::class, 'pivot', 'a1_id', 'a2_id', 'id', 'none_id');
        A2::belongs_to_many('a1', User::class, 'pivot', 'a2_id', 'a1_id', 'none_id', 'id');

        $this->assertEquals(UserN::find(1)->a2->pluck('none_id'), User::find(1)->a2->pluck('none_id'));
        $this->assertEquals(UserN::find(1)->a2()->pluck('none_id'), User::find(1)->a2()->pluck('none_id'));
        $this->assertEquals(UserN::find(1)->a2()->toSql(), User::find(1)->a2()->toSql());
        $this->assertEquals(A2::find(3)->a1->pluck('id'), A2N::find(3)->a1->pluck('id'));
        $this->assertEquals(User::find(1)->a2->first()->pivot->id, UserN::find(1)->a2->first()->pivot->id);
        $this->assertEquals(get_class(User::find(1)->a2->first()->pivot), get_class(UserN::find(1)->a2->first()->pivot));
    }

    public function test_morph_many()
    {
        \Illuminate\Database\Eloquent\Relations\Relation::morphMap([
            'user2' => "Imanghafoori\Relativity\Tests\Normal\User",
            'user1' => User::class,
        ]);

        $this->migrateAndSeed();

        User::morph_many('poly_comments', AttachableComment::class, 'commented', 'morphed_type', 'morphed_id');

        $a1 = User::find(1);
        $a1->poly_comments()->create(['body' => '1', 'user_id' => 1]);
        $a1->poly_comments()->create(['body' => '2', 'user_id' => 2]);
        $a1->poly_comments()->create(['body' => '3', 'user_id' => 3]);

        $this->assertEquals(3, \DB::table('poly_morph_comments')->where("morphed_type", 'user1')->count());
        $this->assertEquals(3, $a1->poly_comments()->count());
        $this->assertEquals(3, $a1->poly_comments->count());

        $a2 = UserN::find(1);
        $a2->poly_comments()->create(['body' => '1', 'user_id' => 1]);
        $a2->poly_comments()->create(['body' => '2', 'user_id' => 2]);
        $a2->poly_comments()->create(['body' => '3', 'user_id' => 3]);

        $this->assertEquals(3, \DB::table('poly_morph_comments')->where("morphed_type", 'user2')->count());
        $this->assertEquals(3, $a2->poly_comments()->count());
        $this->assertEquals(3, $a2->poly_comments->count());

        $this->assertEquals($a1->poly_comments()->toSql(), $a2->poly_comments()->toSql());
    }

    public function test_morph_to_many()
    {
        $this->migrateMorphToMany();

        Post::morph_to_many('tags', Tag::class, 'taggable');
        Tag::morphed_by_many('posts', Post::class, 'taggable');
        Tag::morphed_by_many('videos', Video::class, 'taggable');

        \DB::table('posts')->insert([['name' => 'post_1'], ['name' => 'post_2'], ['name' => 'post_3']]);
        \DB::table('videos')->insert([['name' => 'video_1'], ['name' => 'video_2'], ['name' => 'video_3']]);

        Post::find(1)->tags()->create(['name' => 'tag_1']);
        Post::find(1)->tags()->create(['name' => 'tag_2']);
        Post::find(1)->tags()->create(['name' => 'tag_3']);

        NormalPost::find(1)->tags()->create(['name' => 'tag_1']);
        NormalPost::find(1)->tags()->create(['name' => 'tag_2']);
        NormalPost::find(1)->tags()->create(['name' => 'tag_3']);

        $this->assertEquals(Post::find(1)->tags()->toSql(), NormalPost::find(1)->tags()->toSql());
        $this->assertEquals(NormalTag::find(1)->posts()->toSql(), Tag::find(1)->posts()->toSql());

        $this->assertEquals(3, NormalPost::find(1)->tags()->count());
        $this->assertEquals(3, NormalPost::find(1)->tags->count());

        $this->assertEquals(3, Post::find(1)->tags()->count());
        $this->assertEquals(3, Post::find(1)->tags->count());

        $this->assertEquals(1, Tag::find(1)->posts->first()->id);
        $this->assertEquals(1, Tag::find(1)->posts()->first()->id);
    }

    private function migrateMorphToMany() {

        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->timestamps();
        });
        Schema::create('videos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->timestamps();
        });
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->timestamps();
        });
        Schema::create('taggables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('tag_id');
            $table->unsignedInteger('taggable_id');
            $table->string('taggable_type', 20);

        });
    }

    public function test_has_one()
    {
        $this->migrateAndSeed();
        User::has_one('a4', A4::class, 'a1_id');

        $this->assertEquals(1, User::find(1)->a4->id);
        $this->assertEquals(2, User::find(2)->a4->id);
        $this->assertEquals(3, User::find(3)->a4->id);

        $this->assertEquals(1, User::find(1)->a4()->first()->id);
        $this->assertEquals(2, User::find(2)->a4()->first()->id);
        $this->assertEquals(3, User::find(3)->a4()->first()->id);

        $this->assertEquals(UserN::find(1)->a4()->toSql(), User::find(1)->a4()->toSql());
        $this->assertEquals(UserN::find(2)->a4()->toSql(), User::find(2)->a4()->toSql());
        $this->assertEquals(UserN::find(3)->a4()->toSql(), User::find(3)->a4()->toSql());
    }

    private function migrateAndSeed()
    {
        Schema::defaultStringLength(191);
        Schema::dropAllTables();

        Schema::create('a1', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->timestamps();
        });
        \DB::table('a1')->insert([
            ['name' => 'row1'],
            ['name' => 'row2'],
            ['name' => 'row3'],
        ]);
        Schema::create('a2', function (Blueprint $table) {
            $table->increments('none_id');
            $table->string('name', 20);
            $table->timestamps();
        });
        \DB::table('a2')->insert([
            ['name' => 'row1'],
            ['name' => 'row2'],
            ['name' => 'row3'],
        ]);
        Schema::create('a3', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->integer('user_id');
            $table->integer('a1_d_id');
            $table->integer('a2_id');
            $table->timestamps();
        });
        \DB::table('a3')->insert([
            ['name' => 'row1', 'user_id' => 1, 'a1_d_id' => 1, 'a2_id' => 1],
            ['name' => 'row1', 'user_id' => 1, 'a1_d_id' => 1, 'a2_id' => 2],
            ['name' => 'row1', 'user_id' => 2, 'a1_d_id' => 2, 'a2_id' => 2],
            ['name' => 'row1', 'user_id' => 1, 'a1_d_id' => 1, 'a2_id' => 3],
            ['name' => 'row2', 'user_id' => 2, 'a1_d_id' => 2, 'a2_id' => 3],
            ['name' => 'row3', 'user_id' => 3, 'a1_d_id' => 3, 'a2_id' => 1],
            ['name' => 'row3', 'user_id' => 3, 'a1_d_id' => 3, 'a2_id' => 2],
        ]);
        Schema::create('a4', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->integer('a1_id');
            $table->timestamps();
        });
        \DB::table('a4')->insert([
            ['name' => 'row 1', 'a1_id' => 1],
            ['name' => 'row 2', 'a1_id' => 2],
            ['name' => 'row 3', 'a1_id' => 3],
        ]);
        Schema::create('pivot', function (Blueprint $table) {
            $table->integer('a1_id')->unsigned();
            $table->integer('a2_id')->unsigned();
        });
        \DB::table('pivot')->insert([
            ['a1_id' => 1, 'a2_id' => 1,],
            ['a1_id' => 2, 'a2_id' => 3,],
            ['a1_id' => 3, 'a2_id' => 3,],
        ]);

        Schema::create('poly_morph_comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('body', 30);
            $table->unsignedInteger('morphed_id');
            $table->string('morphed_type', 50);
            $table->timestamps();

//            $table->unique(['user_id', 'morphed_id', 'morphed_type']);
        });
    }
}