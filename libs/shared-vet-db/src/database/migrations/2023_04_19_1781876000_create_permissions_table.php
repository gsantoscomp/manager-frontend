<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePermissionsTable extends Migration
{
    public function up()
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('module', 50);
            $table->string('page', 50);
            $table->string('name', 255);
            $table->unsignedBigInteger('user_type_id');
            $table->timestamps();

            $table->foreign('user_type_id')->references('id')->on('users_types');
        });
    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
}
