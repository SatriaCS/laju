<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->format('l');

        $runningSchedules = auth()->user()
            ->runningSchedules()
            ->where('day', $today)
            ->get();

        return view('dashboard', compact(
            'runningSchedules',
            'today'
        ));
    }
}
