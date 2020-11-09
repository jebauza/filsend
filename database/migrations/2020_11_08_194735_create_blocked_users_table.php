<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockedUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('blocked_users', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('from_user');
            $table->foreign('from_user')->references('id')->on('users');

            $table->unsignedBigInteger('to_user');
            $table->foreign('to_user')->references('id')->on('users');

            $table->timestamps();

            $table->unique(['from_user', 'to_user']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('blocked_users');
    }
}
