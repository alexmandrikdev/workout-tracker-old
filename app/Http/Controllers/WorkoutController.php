<?php

namespace App\Http\Controllers;

use App\Models\Workout;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class WorkoutController extends Controller
{
    public function index(Request $request)
    {

    }

    public function show($date)
    {
        $workout = Workout::whereDate('date', $date)->get();

        return $workout;
    }
}
