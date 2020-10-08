<?php

use App\Http\Controllers\WorkoutImportController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    Route::redirect('/', 'workouts');

    Route::group(['prefix' => 'workouts', 'as' => 'workouts.'], function () {
        Route::get('workouts', function () {
            return view('layouts.app');
        });

        Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
            Route::view('/', 'workouts.import');

            Route::post('/', [WorkoutImportController::class, 'import']);
            Route::post('get-sheets', [WorkoutImportController::class, 'getSheets'])->name('getSheets');
        });
    });

});


