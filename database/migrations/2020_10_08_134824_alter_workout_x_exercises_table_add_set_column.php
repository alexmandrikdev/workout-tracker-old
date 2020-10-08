<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterWorkoutXExercisesTableAddSetColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workout_x_exercises', function (Blueprint $table) {
            $table->unsignedTinyInteger('set')->after('exercise_id');
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
            $table->dropColumn('set');
        });
    }
}
