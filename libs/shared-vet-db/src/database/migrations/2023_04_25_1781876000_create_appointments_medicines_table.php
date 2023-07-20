<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsMedicinesTable extends Migration
{
    public function up()
    {
        Schema::create('appointments_medicines', function (Blueprint $table) {
            $table->unsignedBigInteger('medicine_id');
            $table->unsignedBigInteger('appointment_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('medicine_id')->references('id')->on('medicines');
            $table->foreign('appointment_id')->references('id')->on('appointments');
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments_medicines');
    }
}
