<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsProceduresTable extends Migration
{
    public function up()
    {
        Schema::create('appointments_procedures', function (Blueprint $table) {
            $table->unsignedBigInteger('procedures_id');
            $table->unsignedBigInteger('appointment_id');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->foreign('procedures_id')->references('id')->on('procedures');
            $table->foreign('appointment_id')->references('id')->on('appointments');
        });
    }

    public function down()
    {
        Schema::dropIfExists('appointments_procedures');
    }
}
