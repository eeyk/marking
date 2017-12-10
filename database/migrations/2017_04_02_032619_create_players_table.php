<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->string('activity_id');
            $table->increments('id');
            $table->string('name');
            $table->string('details');
            $table->float('score');
            $table->timestamps();
            $table->softDeletes();
            $table->float('isMarking')->default(0);
            $table->integer('group');
            $table->string('img');
            $table->string('groupName');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('players');
    }
}
