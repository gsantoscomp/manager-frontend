<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email', 100);
            $table->string('password', 100);
            $table->string('name', 255);
            $table->unsignedBigInteger('user_type_id');
            $table->rememberToken();
            $table->timestamps();

            $table->foreign('user_type_id')->references('id')->on('users_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
}
