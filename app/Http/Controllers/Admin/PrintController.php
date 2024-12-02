<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedules;
use App\Models\Teachers;
use App\Models\Classroom;
use Carbon\Carbon;

class PrintController extends Controller
{
    public function print($teacherId){
        $teacher = Teachers::findOrFail($teacherId);
        $schedules = Schedules::where('teacher_id', $teacherId)->get();

        if ($schedules->isEmpty()) {
            return redirect()->back()->with('error', 'No schedules found for this teacher.');
        }

        // Determine year and semester from the first schedule
        $year = $schedules->first()->year;
        $semester = $schedules->first()->semester;

        // Get current school year
        $currentYear = Carbon::now()->year;
        $schoolYear = $currentYear . '-' . ($currentYear + 1);

        return view('admin.schedule-print', compact('schedules', 'year', 'semester', 'teacher', 'schoolYear'));
    }

    public function print_classroom() {
        $classrooms = Classroom::all();

        return view('admin.print-classroom', compact('classrooms'));
    }
}
