<?php

namespace Imanghafoori\Relativity\Tests;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Imanghafoori\Relativity\Tests\RelativeModels\A2;
use Imanghafoori\Relativity\Tests\RelativeModels\A4;
use Imanghafoori\Relativity\Tests\RelativeModels\User;
use Imanghafoori\Relativity\Tests\Normal\User as UserN;

class HasManyThroughTest extends TestCase
{
    public function test_has_many_through_relation()
    {
        $this->migrateAndSeed();

        User::has_many_through('a4s', A4::class, A2::class, null, 'a2_id');

        $this->assertEquals(User::find(1)->a4s()->toSql(), UserN::find(1)->a4s()->toSql());

        $this->assertEquals(User::find(1)->a4s()->first()->id, UserN::find(1)->a4s()->first()->id);
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
            ['name' => 'u1'],
            ['name' => 'u2'],
            ['name' => 'u3'],
        ]);

        Schema::create('a2', function (Blueprint $table) {
            $table->increments('none_id');
            $table->string('name', 20);
            $table->unsignedInteger('user_id');
            $table->timestamps();
        });

        \DB::table('a2')->insert([
            ['name' => 'row1', 'user_id' => 1],
            ['name' => 'row2', 'user_id' => 1],
            ['name' => 'row3', 'user_id' => 1],
        ]);

        Schema::create('a4', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 20);
            $table->unsignedInteger('a2_id');
            $table->timestamps();
        });

        \DB::table('a4')->insert([

            ['name' => 'row1', 'a2_id' => 1],
            ['name' => 'row2', 'a2_id' => 1],
            ['name' => 'row3', 'a2_id' => 1],
        ]);
    }
}
