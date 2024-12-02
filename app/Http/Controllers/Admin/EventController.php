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
    public function printCalendar(Request $request)
    {
        // Determine the current school year
        $currentYear = now()->year;
        $schoolYear = (now()->month >= 6)
            ? $currentYear . '-' . ($currentYear + 1)
            : ($currentYear - 1) . '-' . $currentYear;

        // Base query for events
        $query = Events::query();

        // Check if a specific month is requested
        $monthFilter = $request->input('month');

        // Apply month filter if provided
        if ($monthFilter) {
            $query->whereMonth('startDate', $monthFilter);
        }

        // Order events by start date
        $events = $query->orderBy('startDate', 'asc')->get();

        // Determine month name if a specific month is selected
        $monthName = null;
        if ($monthFilter) {
            $monthName = Carbon::create(null, $monthFilter)->format('F');
        }

        return view('admin.print-calendar-events', [
            'events' => $events,
            'schoolYear' => $schoolYear,
            'monthName' => $monthName
        ]);
    }
}
