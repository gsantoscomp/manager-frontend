<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTypesTable extends Migration
{
    public function up()
    {
        Schema::create('users_types', function (Blueprint $table) {
            $table->id();
            $table->string('type',100);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users_types');
    }
}
