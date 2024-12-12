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

        $query = Events::query();

        // Get the selected month (if any)
        $selectedMonth = $request->input('month');
        $fromDate = $request->input('from');
        $untilDate = $request->input('until');

        // If a specific month is selected
        if ($selectedMonth) {
            $fromDate = Carbon::createFromFormat('Y-m', $currentYear . '-' . $selectedMonth)->startOfMonth();
            $untilDate = Carbon::createFromFormat('Y-m', $currentYear . '-' . $selectedMonth)->endOfMonth();
            $query->whereBetween('startDate', [$fromDate, $untilDate]);
        }
        // If a date range is selected
        elseif ($fromDate && $untilDate) {
            // Convert fromDate and untilDate to Carbon instances
            $fromDate = Carbon::createFromFormat('Y-m-d', $fromDate)->startOfDay();
            $untilDate = Carbon::createFromFormat('Y-m-d', $untilDate)->endOfDay();

            // Check that the untilDate is at least after the fromDate
            if ($untilDate->lt($fromDate)) {
                return redirect()->back()->withErrors(['The "Until" date cannot be earlier than the "From" date.']);
            }

            // Filter events within the date range
            $query->whereBetween('startDate', [$fromDate, $untilDate]);
        }

        $events = $query->orderBy('startDate', 'asc')->get();

        $eventsByMonth = $events->groupBy(function($event) {
            return Carbon::parse($event->startDate)->format('F Y');
        });

        // Define month name for printing
        $monthName = $fromDate ? Carbon::parse($fromDate)->format('F Y') : 'All Events';

        return view('admin.print-calendar-events', compact('eventsByMonth', 'schoolYear', 'fromDate', 'untilDate', 'monthName'));
    }
}
