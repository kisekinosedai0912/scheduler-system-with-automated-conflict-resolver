<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedules;


class PrintController extends Controller
{
    public function print($teacherId){
        $schedules = Schedules::where('teacher_id', $teacherId)->get();
        $year = $schedules->first()->year;
        $semester = $schedules->first()->semester;

        if ($schedules->isEmpty()) {
            return redirect()->back()->with('error', 'No schedules found for this teacher.');
        }

        return view('admin.schedule-print', compact('schedules', 'year', 'semester'));
    }
}
