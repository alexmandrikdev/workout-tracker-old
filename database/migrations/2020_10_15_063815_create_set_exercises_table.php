<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSetExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('set_exercises', function (Blueprint $table) {
            $table->foreignId('set_id')->constrained();
            $table->foreignId('exercise_id')->constrained();
            $table->unsignedTinyInteger('amount');
            $table->foreignId('unit_id')->constrained();
            $table->unsignedTinyInteger('rest_amount')->nullable();
            $table->foreignId('rest_unit_id')->nullable()->constrained('units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('set_exercises');
    }
}
