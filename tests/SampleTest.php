<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Imanghafoori\Relativity\Tests\A2;
use Imanghafoori\Relativity\Tests\A4;
use Imanghafoori\Relativity\Tests\Normal\A1 as A1N;
use Imanghafoori\Relativity\Tests\Normal\A2 as A2N;
use Imanghafoori\Relativity\Tests\Normal\A3 as A3N;
use Imanghafoori\Relativity\Tests\A1;
use Imanghafoori\Relativity\Tests\A3;
use Imanghafoori\Relativity\Tests\Normal\Comment;

class SampleTest extends TestCase
{
    public function test_one_to_many_relation()
    {
        $this->migrateAndSeed();

        A1::has_many('a3', A3::class);
        A1::has_many('a13', A3::class, 'a1_d_id');
        A1::has_many('a13_ordered', A3::class)->orderByDesc('id');
        A3::belongs_to('a1', A1::class);

        $this->assertEquals(A1N::find(1)->a3->count(), A1::find(1)->a3->count());
        $this->assertEquals(A1N::find(1)->a3()->count(), A1::find(1)->a3()->count());
        $this->assertEquals(get_class(A1N::find(1)->a3()), get_class(A1::find(1)->a3()));

        $this->assertEquals(A1N::find(1)->a3->count(), A1::find(1)->a3->count());
        $this->assertEquals(A1N::find(1)->a3()->count(), A1::find(1)->a3()->count());
        $this->assertEquals(get_class(A1N::find(1)->a13()), get_class(A1::find(1)->a13()));
        $this->assertEquals(get_class(A1N::find(1)->a13), get_class(A1::find(1)->a13));

        $this->assertEquals(A1::find(1)->a13_ordered->first()->id, A1N::find(1)->a13_ordered->first()->id);
    }

    public function test_many_to_many_relation()
    {
        $this->migrateAndSeed();

        A1::belongs_to_many('a2', A2::class, 'pivot', 'a1_id', 'a2_id', 'id', 'none_id');
        A2::belongs_to_many('a1', A1::class, 'pivot', 'a2_id', 'a1_id', 'none_id', 'id');

        $this->assertEquals(A1N::find(1)->a2->pluck('none_id'), A1::find(1)->a2->pluck('none_id'));
        $this->assertEquals(A2::find(3)->a1->pluck('id'), A2N::find(3)->a1->pluck('id'));
    }

    public function test_morph_many()
    {
        $this->migrateAndSeed();
        A1::morph_many('comments', Comment::class, 'commented', 'morphed_type', 'morphed_id');

        $a1 = A1::find(1);
        $a1->comments()->create(['a_id' => 1]);
        $a1->comments()->create(['a_id' => 2]);
        $a1->comments()->create(['a_id' => 3]);


        $this->assertEquals(3, \DB::table('poly_morph_comments')->count());
        $this->assertEquals(3, $a1->comments()->count());
        $this->assertEquals(3, $a1->comments->count());

        $a2 = A1N::find(2);

        $a2->comments()->create(['a_id' => 1]);
        $a2->comments()->create(['a_id' => 2]);
        $a2->comments()->create(['a_id' => 3]);

        $this->assertEquals(3, $a2->comments()->count());
        $this->assertEquals(3, $a2->comments->count());

        $this->assertEquals($a1->comments()->toSql(), $a2->comments()->toSql());
    }

    public function test_has_one()
    {
        $this->migrateAndSeed();
        A1::has_one('a4', A4::class);

        $this->assertEquals(1, A1::find(1)->a4->id);
        $this->assertEquals(2, A1::find(2)->a4->id);
        $this->assertEquals(3, A1::find(3)->a4->id);

        $this->assertEquals(1, A1::find(1)->a4()->first()->id);
        $this->assertEquals(2, A1::find(2)->a4()->first()->id);
        $this->assertEquals(3, A1::find(3)->a4()->first()->id);

        $this->assertEquals(A1N::find(1)->a4()->toSql(), A1::find(1)->a4()->toSql());
        $this->assertEquals(A1N::find(2)->a4()->toSql(), A1::find(2)->a4()->toSql());
        $this->assertEquals(A1N::find(3)->a4()->toSql(), A1::find(3)->a4()->toSql());
    }

    private function migrateAndSeed(): void
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
            $table->integer('a1_id');
            $table->integer('a1_d_id');
            $table->integer('a2_id');
            $table->timestamps();
        });
        \DB::table('a3')->insert([
            ['name' => 'row1', 'a1_id' => 1, 'a1_d_id' => 1, 'a2_id' => 1],
            ['name' => 'row1', 'a1_id' => 1, 'a1_d_id' => 1, 'a2_id' => 2],
            ['name' => 'row1', 'a1_id' => 2, 'a1_d_id' => 2, 'a2_id' => 2],
            ['name' => 'row1', 'a1_id' => 1, 'a1_d_id' => 1, 'a2_id' => 3],
            ['name' => 'row2', 'a1_id' => 2, 'a1_d_id' => 2, 'a2_id' => 3],
            ['name' => 'row3', 'a1_id' => 3, 'a1_d_id' => 3, 'a2_id' => 1],
            ['name' => 'row3', 'a1_id' => 3, 'a1_d_id' => 3, 'a2_id' => 2],
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
            $table->unsignedInteger('a_id');
            $table->unsignedInteger('morphed_id');
            $table->string('morphed_type', 50);
            $table->timestamps();

            $table->unique(['a_id', 'morphed_id', 'morphed_type']);
        });
    }
}