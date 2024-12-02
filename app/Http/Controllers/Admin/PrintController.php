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
    public function print(Request $request) {
        // Validate input
        $request->validate([
            'teacher' => 'required|exists:teachers,id',
            'semester' => 'required|string'
        ]);

        // Fetch schedules with all necessary relationships
        $schedules = Schedules::with(['teacher', 'subject', 'classroom'])
            ->where('teacher_id', $request->input('teacher'))
            ->whereHas('subject', function($query) use ($request) {
                $query->where('semester', $request->input('semester'));
            })
            ->get();

        // If no schedules found
        if ($schedules->isEmpty()) {
            return view('errors.no-schedules', [
                'message' => 'No schedules found for the selected teacher and semester.'
            ]);
        }

        // Get teacher details
        $teacher = Teachers::findOrFail($request->input('teacher'));

        // Determine year and school year
        $year = $schedules->first()->year ?? 'N/A';
        $semester = $request->input('semester');
        $currentYear = Carbon::now()->year;
        $schoolYear = $currentYear . '-' . ($currentYear + 1);

        // Return print view
        return view('admin.schedule-print', compact(
            'schedules',
            'teacher',
            'year',
            'semester',
            'schoolYear'
        ));
    }

    public function print_classroom() {
        $classrooms = Classroom::all();

        return view('admin.print-classroom', compact('classrooms'));
    }
}
