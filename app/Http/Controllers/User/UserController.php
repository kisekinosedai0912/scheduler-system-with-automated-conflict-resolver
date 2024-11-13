<?php

namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Events;
use App\Models\Classroom;
use App\Models\Schedules;
use App\Models\Subjects;
use App\Models\Teachers;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    // Function to show all events in the calendar (view only)
    public function userIndex() {
        $events = array(); // Create an array of events
        $calendarEvents = Events::all(); // Retrieve all events in the events table model
        $prevEventStartTime = $calendarEvents->isNotEmpty() ? $calendarEvents->first()->startTime : null;

        foreach ($calendarEvents as $calendarEvent) {
            $events[] = [
                'id' => $calendarEvent->id,
                'title' => $calendarEvent->eventTitle,
                'start' => Carbon::parse($calendarEvent->startDate . ' ' . $calendarEvent->startTime)->format('Y-m-d\TH:i:s'),
                'end' => Carbon::parse($calendarEvent->endDate . ' ' . $calendarEvent->endTime)->format('Y-m-d\TH:i:s'),
                'color' => 'green',
            ];
        }
        return view('event-home', ['events' => $events, 'prevEventStartTime' => $prevEventStartTime]);
    }

    // Function to display the schedules of the authenticated user
    public function facultySchedule() {
        $user = Auth::user();

        if (!$user || $user->user_role !== 'faculty' || !$user->teacher_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $schedules = Schedules::where('teacher_id', $user->teacher_id) // Fetch schedules for the current logged in faculty
            ->with(['subject', 'classroom', 'teacher'])
            ->get();

        $schedules->each(function ($schedule) { // Use the model's hasConflict method in determining conflicts
            $schedule->is_conflicted = $schedule->hasConflict();
        });

        $subjects = Subjects::all();
        $classrooms = Classroom::all();

        return view('faculty-schedule', [
            'schedules' => $schedules,
            'subjects' => $subjects,
            'classrooms' => $classrooms,
        ]);
    }

    // Function to display the notification to the user
    public function notification() {
        $userId = auth()->id();

        $notifications = Notifications::with('event') // Fetch notifications for the authenticated user along with the associated event
        ->where('user_id', $userId)
        ->orderBy('created_at', 'desc')
        ->get();

        return view('notifications', ['notifications' => $notifications]);
    }

    // Function to mark the notification as read or unread
    public function is_read(Request $request, $id) {
        $notification = Notifications::find($id);

        if ($notification) {
            $notification->is_read = true; // Mark as read, boolean value true or false
            $notification->save(); // Save the notification
            return response()->json(['success' => 'Notification marked as read']);
        }

        return response()->json(['error' => 'Notification not found'], 404);
    }

    // Function to display the conflicted schedules for the user
    public function conflicted_schedule() {
        $user = Auth::user();

        if (!$user || $user->user_role !== 'faculty' || !$user->teacher_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $schedules = Schedules::where('teacher_id', $user->teacher_id) // Fetch schedules specifically for the current logged-in faculty user
            ->with(['subject', 'classroom'])
            ->get();

        // Initialized empty arrays that will be used to store the conflicted and unique scheds of the logged in user
        $conflictedSchedules = [];
        $uniqueSchedules = [];
        $conflictCount = 0;

        // Iterate through schedules and check for conflicts
        foreach ($schedules as $schedule) {
            // Use the hasConflict method to determine if the schedule is conflicting, the method is located in the model initialization
            $isConflicted = $schedule->hasConflict();

            if ($isConflicted) {
                $conflictedSchedules[] = $schedule;
                $conflictCount++;
            } else {
                $uniqueSchedules[] = $schedule;
            }
        }

        return view('conflicts', [
            'conflictedSchedules' => $conflictedSchedules,
            'subjectCount' => count($uniqueSchedules),
            'conflictCount' => $conflictCount,
        ]);
    }

    // Function to get the teacher's conflicted schedules to print
    public function printConflictedSchedules() {
        $user = Auth::user();

        if (!$user || $user->user_role !== 'faculty' || !$user->teacher_id) {
            return redirect()->back()->with('error', 'Access denied.');
        }

        $schedules = Schedules::where('teacher_id', $user->teacher_id) // Fetch all schedules for the logged in user
            ->with(['subject', 'classroom', 'teacher'])
            ->get();

        $conflictedSchedules = $schedules->filter(function ($schedule) {  // Identify conflicted schedules using the hasConflict method defined from the model
            return $schedule->hasConflict();
        });

        $conflictCount = $conflictedSchedules->count(); // Count the scheds that are conflicting
        $totalSchedules = $schedules->count(); // Count the scheds that are not conflicting

        return view('print-conflict-schedule', [
            'conflictedSchedules' => $conflictedSchedules,
            'conflictCount' => $conflictCount,
            'totalSchedules' => $totalSchedules,
        ]);
    }
}
