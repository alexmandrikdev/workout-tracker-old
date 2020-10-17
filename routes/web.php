<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\WorkoutController;
use App\Http\Controllers\WorkoutImportController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth'], function () {

    Route::redirect('/', 'calendar');

    Route::group(['prefix' => 'workouts', 'as' => 'workouts.'], function () {
        Route::group(['prefix' => 'import', 'as' => 'import.'], function () {
            Route::view('/', 'workouts.import');

            Route::post('/', [WorkoutImportController::class, 'import']);
            Route::post('get-sheets', [WorkoutImportController::class, 'getSheets'])->name('getSheets');
            Route::post('get-import-progress', [WorkoutImportController::class, 'getImportProgress']);
        });
    });

    // Route::resource('workouts', WorkoutController::class)->only(['index', 'show']);

    Route::resource('calendar', CalendarController::class)->only(['index']);
});
