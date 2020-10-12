<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitIdToWorkoutXExercisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workout_x_exercises', function (Blueprint $table) {
            $table->dropColumn('unit');
            $table->dropColumn('rest_unit');

            $table->foreignId('unit_id')->constrained()->after('amount');
            $table->foreignId('rest_unit_id')->constrained('units')->after('rest_amount');
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
