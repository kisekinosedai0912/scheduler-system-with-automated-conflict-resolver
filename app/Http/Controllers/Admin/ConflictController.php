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

class ConflictController extends Controller
{
    // Function for returning view in the schedule page of the admin dashboard
    public function schedules() {
        $schedules = Schedules::with(['teacher', 'subject', 'classroom'])->withTrashed()->get();

        // Set is_conflicted attribute for each schedule
        foreach ($schedules as $schedule) {
            $schedule->is_conflicted = $schedule->hasConflict();
            \Log::info("Schedule ID: {$schedule->id}, Is Conflicted: {$schedule->is_conflicted}");
        }

        $teachers = Teachers::withTrashed()->get();
        $subjects = Subjects::all();
        $classrooms = Classroom::all();

        return view('admin.schedules', compact('schedules', 'teachers', 'subjects', 'classrooms'));
    }

    // Function for editing schedules
    public function editSchedule(Schedules $schedules) {
        $schedules = Schedules::with(['teacher', 'subject', 'classroom'])->get();
        $teachers = Teachers::withTrashed()->get();
        $subjects = Subjects::all();
        $classrooms = Classroom::all();

        return view('admin.schedules', compact('schedules', 'teachers', 'subjects', 'classrooms'));
    }

    // Function for updating schedule
    public function updateSchedule(Request $request, Schedules $schedules){
        // Convert the start and end time into a 12-hour format and requests an update of the data with formatted times before storing in the database table
        $request->merge([
            'startTime' => Carbon::createFromFormat('h:i A', $request->startTime)->format('H:i:s'),
            'endTime' => Carbon::createFromFormat('h:i A', $request->endTime)->format('H:i:s'),
        ]);

        $hasConflict = $this->checkForConflicts($request->teacher_id, $request->startTime, $request->endTime, $schedules->id);

        $request->validate([
            'teacher_id' => 'required',
            'semester' => 'required|string',
            'categoryName' => 'required',
            'days' => 'required|array',
            'subject_id' => 'required',
            'studentNum' => 'required',
            'yearSection' => 'nullable|string',
            'room_id' => 'required',
            'startTime' => 'required|date_format:H:i:s',
            'endTime' => 'required|date_format:H:i:s',
        ]);

        $days = implode('-', $request->input('days', []));

        try {
            // Store the old duration to adjust teacher's hours
            $oldStartTime = \Carbon\Carbon::parse($schedules->startTime);
            $oldEndTime = \Carbon\Carbon::parse($schedules->endTime);
            $oldDuration = $oldStartTime->diffInHours($oldEndTime, true);

            $schedules->update([
                'teacher_id' => $request->input('teacher_id'),
                'semester' => $request->input('semester'),
                'categoryName' => $request->input('categoryName'),
                'subject_id' => $request->input('subject_id'),
                'room_id' => $request->input('room_id'),
                'studentNum' => $request->input('studentNum'),
                'yearSection' => $request->input('yearSection'),
                'days' => $days,
                'startTime' => $request->input('startTime'),
                'endTime' => $request->input('endTime'),
            ]);

            // Calculate and update teacher's hours
            $newStartTime = \Carbon\Carbon::parse($schedules->startTime);
            $newEndTime = \Carbon\Carbon::parse($schedules->endTime);
            $newDuration = $newStartTime->diffInHours($newEndTime, true);

            $teacher = $schedules->teacher;
            if ($teacher) {
                // Adjust total hours by subtracting old duration and adding new duration
                $teacher->numberHours = max(0, $teacher->numberHours - $oldDuration + $newDuration);
                $teacher->save();
            }

            // Check for conflicts after creating the schedule
            $schedules->is_conflicted = $hasConflict;
            $schedules->save();

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Schedule updated successfully!');
    }

    // Function for creating schedules
    public function createSchedule(Request $request) {
        $request->merge([
            'startTime' => Carbon::createFromFormat('h:i A', $request->startTime)->format('H:i:s'),
            'endTime' => Carbon::createFromFormat('h:i A', $request->endTime)->format('H:i:s'),
        ]);

        $hasConflict = $this->checkForConflicts($request->teacher_id, $request->startTime, $request->endTime);

        $request->validate([
            'teacher_id' => 'required',
            'semester' => 'required|string',
            'categoryName' => 'required',
            'days' => 'required|array',
            'subject_id' => 'required',
            'studentNum' => 'required|integer',
            'yearSection' => 'nullable|string',
            'room_id' => 'required',
            'startTime' => 'required|date_format:H:i:s',
            'endTime' => 'required|date_format:H:i:s',
        ]);

        $days = implode('-', $request->input('days', []));

        // If there's a conflict and no selected slot, return a conflict response
        if ($hasConflict && !$request->has('selected_slot')) {
            // Fetch the full teacher and subject details
            $teacher = Teachers::findOrFail($request->input('teacher_id'));
            $subject = Subjects::findOrFail($request->input('subject_id'));

            $availableSlots = $this->getAvailableTimeSlots($request->teacher_id, $request->days, $request->startTime, $request->endTime);
            return response()->json([
                'status' => 'conflict',
                'message' => 'Schedule conflicts with existing schedules.',
                'available_slots' => $availableSlots,
                'original_schedule' => [
                    'teacher_id' => $request->input('teacher_id'),
                    'teacher' => [
                        'id' => $teacher->id,
                        'teacherName' => $teacher->teacherName
                    ],
                    'semester' => $request->input('semester'),
                    'categoryName' => $request->input('categoryName'),
                    'subject_id' => $request->input('subject_id'),
                    'subject' => [
                        'id' => $subject->id,
                        'subjectName' => $subject->subjectName
                    ],
                    'room_id' => $request->input('room_id'),
                    'studentNum' => $request->input('studentNum'),
                    'yearSection' => $request->input('yearSection'),
                    'days' => $days,
                    'startTime' => $request->input('startTime'),
                    'endTime' => $request->input('endTime'),
                ]
            ], 409); // Conflict status code
        }

        // If a selected slot is provided during conflict, use its details
        if ($hasConflict && $request->has('selected_slot')) {
            $selectedSlot = json_decode($request->input('selected_slot'), true);

            // Override the original time and day with the selected slot
            $days = $selectedSlot['day'];
            $request->merge([
                'startTime' => Carbon::createFromFormat('H:i', $selectedSlot['startTime'])->format('H:i:s'),
                'endTime' => Carbon::createFromFormat('H:i', $selectedSlot['endTime'])->format('H:i:s'),
            ]);
        }


        try {
            $schedule = Schedules::create([
                'teacher_id' => $request->input('teacher_id'),
                'semester' => $request->input('semester'),
                'categoryName' => $request->input('categoryName'),
                'subject_id' => $request->input('subject_id'),
                'room_id' => $request->input('room_id'),
                'studentNum' => $request->input('studentNum'),
                'yearSection' => $request->input('yearSection'),
                'days' => $days,
                'startTime' => $request->input('startTime'),
                'endTime' => $request->input('endTime'),
            ]);

            $schedule->is_conflicted = $hasConflict;
            $schedule->save();

            // Calculate and update teacher's hours
            $schedule->calculateAndUpdateTeacherHours();

            if ($hasConflict) {
                $availableSlots = $this->getAvailableTimeSlots($request->teacher_id, $request->days, $request->startTime, $request->endTime);
                return response()->json([
                    'status' => 'conflict',
                    'available_slots' => $availableSlots,
                    'schedule' => [
                        'id' => $schedule->id,
                        'teacher' => $schedule->teacher,
                        'subject' => $schedule->subject,
                        'semester' => $schedule->semester,
                        'days' => $schedule->days,
                        'startTime' => $schedule->startTime,
                        'endTime' => $schedule->endTime,
                    ]
                ]);
            }

            return redirect()->route('admin.schedules')->with('success', 'Schedule added successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Method to get available time slots for a given day
    private function getAvailableTimeSlots($teacherId, $days, $requestedStartTime, $requestedEndTime)
    {
        $availableSlots = [];
        $timeSlots = [
            ['start_time' => '08:00', 'end_time' => '09:00'],
            ['start_time' => '09:00', 'end_time' => '10:00'],
            ['start_time' => '10:00', 'end_time' => '11:00'],
            ['start_time' => '11:00', 'end_time' => '12:00'],
            ['start_time' => '13:00', 'end_time' => '14:00'],
            ['start_time' => '14:00', 'end_time' => '15:00'],
            ['start_time' => '15:00', 'end_time' => '16:00'],
            ['start_time' => '16:00', 'end_time' => '17:00'],
        ];

        foreach ($days as $day) {
            $isFullyBooked = true;

            foreach ($timeSlots as $slot) {
                // Check if the teacher is already booked in this time slot
                if (!$this->checkForConflicts($teacherId, $slot['start_time'], $slot['end_time'])) {
                    // If the slot is available, add it to the available slots array
                    $availableSlots[] = [
                        'day' => $day,
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ];
                    $isFullyBooked = false; // Mark that the teacher is not fully booked
                }
            }

            // If no slots were found for the current day, check the next day
            if ($isFullyBooked) {
                // Get the next day
                $nextDay = $this->getNextDay($day);
                // Check availability for the next day
                foreach ($timeSlots as $slot) {
                    if (!$this->checkForConflicts($teacherId, $slot['start_time'], $slot['end_time'])) {
                        $availableSlots[] = [
                            'day' => $nextDay,
                            'start_time' => $slot['start_time'],
                            'end_time' => $slot['end_time'],
                        ];
                    }
                }
            }
        }

        return $availableSlots;
    }

    // Helper method to get the next day of the week
    private function getNextDay($currentDay)
    {
        $daysOfWeek = ['M', 'T', 'W', 'TH', 'F'];
        $currentIndex = array_search($currentDay, $daysOfWeek);

        // If it's the last day (Friday), return Monday
        if ($currentIndex === 4) {
            return 'M';
        }

        // Return the next day in the week
        return $daysOfWeek[$currentIndex + 1];
    }

    // Function to fetch all the conflicted details
    public function getConflictDetails(Request $request) {
        $teacherId = $request->input('teacher_id');
        $conflictedScheduleId = $request->input('conflicted_schedule_id');

        // Fetch conflicted schedule details and available slots for the teacher
        $conflictedSchedule = Schedule::find($conflictedScheduleId);
        $teacher = Teacher::find($teacherId);

        // Get available time slots based on teacher's availability
        $availableSlots = $this->getAvailableTimeSlots($teacherId, $conflictedSchedule->days);

        return response()->json([
            'teacherName' => $teacher->name,
            'conflictedSchedule' => $conflictedSchedule->formatted_schedule,
            'availableSlots' => $availableSlots,
        ]);
    }


    // Method to get available days
    private function getAvailableDaysForTeacher($teacherId, $startTime, $endTime)
    {
        $alternativeDays = [];
        $daysOfWeek = ['M', 'T', 'W', 'TH', 'F'];

        foreach ($daysOfWeek as $day) {
            // Check if the teacher is fully booked on this day
            if (!$this->isTeacherFullyBookedForDay($teacherId, $day, $startTime, $endTime)) {
                $alternativeDays[] = $day;  // Add available day to the list
            }
        }

        return $alternativeDays;
    }

    // Check if the teacher is fully booked for a given day and time
    private function isTeacherFullyBookedForDay($teacherId, $day, $startTime, $endTime)
    {
        // Check for conflicts using the existing logic for the teacher's schedule
        return $this->checkForConflicts($teacherId, $startTime, $endTime, null);
    }

    // Existing conflict check function, which you already have in place
    private function checkForConflicts($teacherId, $startTime, $endTime, $exceptId = null)
    {
        $conflictingSchedules = Schedules::where('teacher_id', $teacherId)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('startTime', [$startTime, $endTime])
                    ->orWhereBetween('endTime', [$startTime, $endTime])
                    ->orWhere(function($query) use ($startTime, $endTime) {
                        $query->where('startTime', '<=', $startTime)
                            ->where('endTime', '>=', $endTime);
                    });
            });

        if ($exceptId) {
            $conflictingSchedules->where('id', '!=', $exceptId);
        }

        return $conflictingSchedules->exists();
    }


    // Function for deletion of schedules
    public function deleteSchedule($id) {
        try {
            $schedule = Schedules::withTrashed()->findOrFail($id);
            $teacher = $schedule->teacher;

            if ($teacher) {
                $startTime = \Carbon\Carbon::parse($schedule->startTime);
                $endTime = \Carbon\Carbon::parse($schedule->endTime);
                $duration = $startTime->diffInHours($endTime, true);

                $teacher->numberHours = max(0, $teacher->numberHours - $duration); // Prevent negative hours
                $teacher->save();
            }

            $schedule->forceDelete();

            return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully.');

        } catch (\Exception $e) {
            return redirect()->route('admin.schedules')->with('error', 'Error occurred while deleting the record: ' . $e->getMessage());
        }
    }
}
