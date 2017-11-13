<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->string('activity_id');
            $table->increments('id');
            $table->string('name');
            $table->string('account')->unique();
            $table->string('password', 60);
            $table->float('weight');
            $table->string('details');
            $table->rememberToken();
            $table->timestamps();
            $table->boolean('isAdmin');
            $table->softDeletes();
            $table->string('level');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }
}
