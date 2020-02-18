<?php

namespace Imanghafoori\Relativity\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Eloquent\Relations\Relation;
use Imanghafoori\Relativity\Tests\RelativeModels\Tag;
use Imanghafoori\Relativity\Tests\RelativeModels\Post;
use Imanghafoori\Relativity\Tests\RelativeModels\User;
use Imanghafoori\Relativity\Tests\Normal\User as UserN;
use Imanghafoori\Relativity\Tests\RelativeModels\Video;
use Imanghafoori\Relativity\Tests\Normal\Tag as NormalTag;
use Imanghafoori\Relativity\Tests\Normal\Post as NormalPost;
use Imanghafoori\Relativity\Tests\RelativeModels\AttachableComment;
use Imanghafoori\Relativity\Tests\Normal\AttachableComment as AttachableCommentN;

class PolymorphicRelationsTest extends TestCase
{
    public function test_morph_many()
    {
        Relation::morphMap([
            'user2' => "Imanghafoori\Relativity\Tests\Normal\User",
            'user1' => User::class,
        ]);

        $this->migrateAndSeed();

        User::morph_many('poly_comments', AttachableComment::class, 'commented', 'morphed_type', 'morphed_id');

        AttachableComment::morph_to('commentable', 'morphed_type', 'morphed_id');

        $a1 = User::find(1);
        $a1->poly_comments()->create(['body' => '1', 'user_id' => 1]);
        $a1->poly_comments()->create(['body' => '2', 'user_id' => 2]);
        $a1->poly_comments()->create(['body' => '3', 'user_id' => 3]);

        $this->assertEquals(
            AttachableComment::find(1)->commentable()->toSql(),
            AttachableCommentN::find(1)->commentable()->toSql()
        );
        $this->assertEquals(3, \DB::table('poly_morph_comments')->where('morphed_type', 'user1')->count());
        $this->assertEquals(3, $a1->poly_comments()->count());
        $this->assertEquals(3, $a1->poly_comments->count());

        $a2 = UserN::find(1);
        $a2->poly_comments()->create(['body' => '1', 'user_id' => 1]);
        $a2->poly_comments()->create(['body' => '2', 'user_id' => 2]);
        $a2->poly_comments()->create(['body' => '3', 'user_id' => 3]);

        $this->assertEquals(3, \DB::table('poly_morph_comments')->where('morphed_type', 'user2')->count());
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

    private function migrateMorphToMany()
    {
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
            $table->string('taggable_type', 60);
        });
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
