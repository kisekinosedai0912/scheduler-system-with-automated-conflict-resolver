<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Classroom;
use App\Models\Events;
use App\Models\Schedules;
use App\Models\Subjects;
use App\Models\Teachers;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;

class EventController extends Controller
{
    public function printCalendar()
    {
        // Determine the current school year
        $currentYear = now()->year;
        $schoolYear = (now()->month >= 6)
            ? $currentYear . '-' . ($currentYear + 1)
            : ($currentYear - 1) . '-' . $currentYear;

        // Fetch events for the current school year
        $events = Events::whereBetween('startDate', [
            now()->startOfYear(),
            now()->endOfYear()
        ])->orderBy('startDate', 'asc')->get();

        // You can fetch school name from a configuration or database
        // $schoolName = config('app.school_name', 'Your School Name');

        return view('admin.print-calendar-events', [
            'events' => $events,
            'schoolYear' => $schoolYear,
            // 'schoolName' => $schoolName
        ]);
    }
}
