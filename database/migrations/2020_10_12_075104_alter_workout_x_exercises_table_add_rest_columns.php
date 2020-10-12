<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWorkoutXExercisesTableAddRestColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workout_x_exercises', function (Blueprint $table) {
            $table->enum('rest_unit', ['s', 'rep', 'reps', 'min', 'hour'])->after('unit');
            $table->unsignedTinyInteger('rest_amount')->nullable()->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workout_x_exercises', function (Blueprint $table) {
            //
        });
    }
}
