<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkoutXExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workout_x_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained();
            $table->foreignId('exercise_id')->constrained();
            $table->unsignedTinyInteger('amount');
            $table->enum('unit', ['s', 'rep', 'reps', 'min', 'hour']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('workout_x_exercises');
    }
}
