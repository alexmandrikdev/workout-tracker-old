<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\Unit;
use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class WorkoutController extends Controller
{
    public function show($date)
    {
        $workout = Workout::whereDate('date', $date)->get();

        return $workout;
    }
}
