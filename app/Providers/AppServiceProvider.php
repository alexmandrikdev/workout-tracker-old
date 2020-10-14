<?php

namespace App\Providers;

use App\Models\Workout;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function($view){
            $workoutNames = Workout::all()->pluck('name')->unique();

            $view->with('workoutNames', $workoutNames);
        });
    }
}
