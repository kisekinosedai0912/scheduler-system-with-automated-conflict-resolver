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
            // \Log::info("Schedule ID: {$schedule->id}, Is Conflicted: {$schedule->is_conflicted}");
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
        try {
            $startTime = $request->input('startTime');
            $endTime = $request->input('endTime');

            // Attempt to parse times with multiple formats
            $parsedStartTime = $this->parseTime($startTime);
            $parsedEndTime = $this->parseTime($endTime);

            $request->merge([
                'startTime' => $parsedStartTime,
                'endTime' => $parsedEndTime,
            ]);

            $hasConflict = $this->checkForConflicts(
                $request->teacher_id,
                $parsedStartTime,
                $parsedEndTime,
                $schedules->id,
                $daysString,
                $request->input('section') // Pass the section here
            );
            $request->validate([
                'teacher_id' => 'required',
                'semester' => 'required|string',
                'categoryName' => 'required',
                'days' => 'required|array',
                'subject_id' => 'required',
                // 'studentNum' => 'required',
                'year' => 'required|string',
                'section' => 'required|string',
                'room_id' => 'required',
                'startTime' => 'required|date_format:H:i:s',
                'endTime' => 'required|date_format:H:i:s',
            ]);

            $days = implode('-', $request->input('days', []));

            // Storing the old duration to adjust teacher's hours
            $oldStartTime = \Carbon\Carbon::parse($schedules->startTime);
            $oldEndTime = \Carbon\Carbon::parse($schedules->endTime);
            $oldDuration = $oldStartTime->diffInHours($oldEndTime, true);

            $schedules->update([
                'teacher_id' => $request->input('teacher_id'),
                'semester' => $request->input('semester'),
                'categoryName' => $request->input('categoryName'),
                'subject_id' => $request->input('subject_id'),
                'room_id' => $request->input('room_id'),
                // 'studentNum' => $request->input('studentNum'),
                'year' => $request->input('year'),
                'section' => $request->input('section'),
                'days' => $days,
                'startTime' => $parsedStartTime,
                'endTime' => $parsedEndTime,
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
            \Log::error('Schedule update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Schedule updated successfully!');
    }

    // Function for creating schedules
    public function createSchedule(Request $request) {
        // For Debugging: Loggin the incoming request data
        \Log::info('Create Schedule Request Data', [
            'all_data' => $request->all(),
            'selected_slots' => $request->input('selected_slots'),
            'days' => $request->input('days')
        ]);

        try {
            // Ensure days is an array
            $days = $request->input('days');
            if (is_string($days)) {
                $days = explode(',', $days); // Change from '-' to ',' if using multi-select
            }

            // Validate that days is an array
            if (!is_array($days)) {
                throw new \Exception("The days field must be an array.");
            }

            // Normalize days array (trim and remove spaces & empty values)
            $days = array_filter(array_map('trim', $days));
            \Log::info('Processed Days', ['days' => $days]);

            if (empty($days)) {
                throw new \Exception("Please select at least one day.");
            }

            $startTime = $request->input('startTime');
            $endTime = $request->input('endTime');

           // If selected slots are provided, override days and times
            if ($request->has('selected_slots')) {
                $selectedSlots = json_decode($request->input('selected_slots'), true);

                if (!empty($selectedSlots)) {
                    // Extract days from selected slots
                    $days = array_column($selectedSlots, 'day');

                    // Use the first slot's time
                    $startTime = $selectedSlots[0]['startTime'];
                    $endTime = $selectedSlots[0]['endTime'];

                    // Reparse times if needed
                    $parsedStartTime = $this->parseTime($startTime);
                    $parsedEndTime = $this->parseTime($endTime);

                    // Update request with new days and times
                    $request->merge([
                        'days' => $days,
                        'startTime' => $parsedStartTime,
                        'endTime' => $parsedEndTime,
                    ]);
                }
            }

            // Attempt to parse times with multiple formats
            $parsedStartTime = $this->parseTime($startTime);
            $parsedEndTime = $this->parseTime($endTime);

            // Convert days array to be seperated by a hyphen/dash when storing in the database
            $daysString = implode('-', $days);

            // Merge parsed data back into request
            $request->merge([
                'days' => $days,
                'startTime' => $parsedStartTime,
                'endTime' => $parsedEndTime,
            ]);

            // Validate before checking conflicts
            $request->validate([
                'teacher_id' => 'required|exists:teachers,id',
                'semester' => 'required|string',
                'categoryName' => 'required',
                'days' => 'required|array|min:1',
                'subject_id' => 'nullable',
                'year' => [
                    'required',
                    'string',
                    'in:Grade 11,Grade 12'
                ],
                'section' => 'required|string',
                'room_id' => 'required',
                'startTime' => 'required|date_format:H:i:s',
                'endTime' => 'required|date_format:H:i:s',
            ], [
                'year.required' => 'The year/grade field is required.',
                'year.in' => 'Please select either Grade 11 or Grade 12.',
            ]);

            // Check for conflicts using the checkForConflicts method
            $hasConflict = $this->checkForConflicts(
                $request->teacher_id,
                $parsedStartTime,
                $parsedEndTime,
                null, // No exceptId for new schedules
                $daysString,
                $request->input('section') // Pass the section here
            );

            // If there's a conflict and no selected slot, return conflict response
            if ($hasConflict && !$request->has('selected_slot')) {
                // Fetch the full teacher and subject details
                $teacher = Teachers::findOrFail($request->input('teacher_id'));
                $subject = Subjects::findOrFail($request->input('subject_id'));

                $availableSlots = $this->getAvailableTimeSlots($request->teacher_id, $days, $parsedStartTime, $parsedEndTime);
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
                        'yearSection' => $request->input('yearSection'),
                        'days' => $daysString,
                        'startTime' => $parsedStartTime,
                        'endTime' => $parsedEndTime,
                    ]
                ], 409); // Conflict status code
            }

             // Create a single schedule entry for all selected days
            try {
                $schedule = Schedules::create([
                    'teacher_id' => $request->input('teacher_id'),
                    'semester' => $request->input('semester'),
                    'categoryName' => $request->input('categoryName'),
                    'subject_id' => $request->input('subject_id'),
                    'room_id' => $request->input('room_id'),
                    'year' => $request->input('year'),
                    'section' => $request->input('section'),
                    'days' => $daysString,
                    'startTime' => $parsedStartTime,
                    'endTime' => $parsedEndTime,
                ]);

                // Calculate and update teacher's hours
                $schedule->calculateAndUpdateTeacherHours();

            } catch (\Exception $e) {
                \Log::error('Schedule creation error', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'request_data' => $request->all()
                ]);
                return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
            }

            return redirect()->route('admin.schedules')->with('success', 'Schedule added successfully!');

        } catch (\Exception $e) {
            \Log::error('Time Parsing Error', [
                'message' => $e->getMessage(),
                'start_time' => $startTime ?? 'N/A',
                'end_time' => $endTime ?? 'N/A',
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Invalid time format: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Parse time from various formats
     *
     * @param string $time
     * @return string Parsed time in 'H:i:s' format
     * @throws \Exception If time cannot be parsed
     */
    private function parseTime($time) {
        // List of possible time formats to try
        $formats = [
            'H:i A',
            'H:i:s',
            'H:i',
            'h:i A',
            'h:i:s A',
            'h:i a',  // Lowercase am/pm
            'H:i a'   // Additional lowercase format
        ];

        // Try parsing with different formats
        foreach ($formats as $format) {
            try {
                $parsedTime = Carbon::createFromFormat($format, $time);
                return $parsedTime->format('H:i:s');
            } catch (\Exception $e) {
                // Continue to next format
                continue;
            }
        }

        // If no format works, log the problematic time and throw an exception
        \Log::warning("Unable to parse time: {$time}");
        throw new \Exception("Unable to parse time: {$time}");
    }

    // Method to get available time slots for a given day
    private function getAvailableTimeSlots($teacherId, $days, $requestedStartTime, $requestedEndTime) {
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

        // Fetch all existing schedules for the teacher
        $existingSchedules = Schedules::where('teacher_id', $teacherId)->get();

        foreach ($days as $day) {
            foreach ($timeSlots as $slot) {
                // Check if this time slot conflicts with any existing schedules
                $hasConflict = $existingSchedules->first(function ($schedule) use ($slot, $day) {
                    // Check if the schedule is on the same day
                    $scheduleDays = explode('-', $schedule->days);
                    if (!in_array($day, $scheduleDays)) {
                        return false;
                    }

                    // Check for time overlap
                    return (
                        ($slot['start_time'] >= $schedule->startTime && $slot['start_time'] < $schedule->endTime) ||
                        ($slot['end_time'] > $schedule->startTime && $slot['end_time'] <= $schedule->endTime) ||
                        ($schedule->startTime >= $slot['start_time'] && $schedule->startTime < $slot['end_time'])
                    );
                });

                // If no conflict is found, add the slot to available slots
                if (!$hasConflict) {
                    $availableSlots[] = [
                        'day' => $day,
                        'start_time' => $slot['start_time'],
                        'end_time' => $slot['end_time'],
                    ];
                }
            }
        }

        // Additional filtering to remove slots that are completely within existing schedules
        $availableSlots = array_filter($availableSlots, function($slot) use ($existingSchedules, $days) {
            foreach ($existingSchedules as $schedule) {
                // Check if the schedule is on the same day
                $scheduleDays = explode('-', $schedule->days);
                $dayMatch = array_intersect($scheduleDays, $days);

                if ($dayMatch) {
                    // Check if the slot is completely within an existing schedule
                    if ($slot['start_time'] >= $schedule->startTime && $slot['end_time'] <= $schedule->endTime) {
                        return false;
                    }
                }
            }
            return true;
        });

        return array_values($availableSlots); // Reindex the array
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

   // Modify the checkForConflicts method to handle multiple days
    private function checkForConflicts($teacherId, $startTime, $endTime, $exceptId = null, $days = null, $section = null)
    {
        // Convert days string to array if provided
        $newScheduleDays = $days ? explode('-', $days) : [];

        // Start building the query
        $conflictingSchedules = Schedules::where('teacher_id', $teacherId)
            ->where(function($query) use ($startTime, $endTime) {
                $query->where(function($query) use ($startTime, $endTime) {
                    // Check if the new schedule overlaps with existing schedules
                    $query->where('startTime', '<', $endTime)
                        ->where('endTime', '>', $startTime);
                });
            });

        // Exclude the current schedule if provided
        if ($exceptId) {
            $conflictingSchedules->where('id', '!=', $exceptId);
        }

        // Get the conflicting schedules
        $conflicts = $conflictingSchedules->get();

        // If no specific days are provided, return false if there are no conflicts
        if (empty($newScheduleDays)) {
            return $conflicts->isNotEmpty();
        }

        // Check if any of the conflicting schedules occur on the same day
        foreach ($conflicts as $conflict) {
            $conflictDays = explode('-', $conflict->days);

            // Check for day conflicts
            foreach ($newScheduleDays as $newDay) {
                if (in_array($newDay, $conflictDays)) {
                    // If section is provided, check for section match
                    if ($section === null || $conflict->section === $section) {
                        return true; // Conflict found
                    }
                }
            }
        }

        return false; // No conflicts found
    }

    // private function checkForConflicts($teacherId, $startTime, $endTime, $exceptId = null, $days = null, $section = null)
    // {
    //     // Start building the query
    //     $conflictingSchedules = Schedules::where('teacher_id', $teacherId)
    //         ->where(function($query) use ($startTime, $endTime) {
    //             $query->where(function($query) use ($startTime, $endTime) {
    //                 // Check if the new schedule overlaps with existing schedules
    //                 $query->where(function($query) use ($startTime, $endTime) {
    //                     $query->where('startTime', '<', $endTime) // Existing starts before new ends
    //                         ->where('endTime', '>', $startTime); // Existing ends after new starts
    //                 });
    //             });
    //         });

    //     // Exclude the current schedule if provided
    //     if ($exceptId) {
    //         $conflictingSchedules->where('id', '!=', $exceptId);
    //     }

    //     // Get the conflicting schedules
    //     $conflicts = $conflictingSchedules->get();

    //     // If no specific days are provided, return false if there are no conflicts
    //     if (is_null($days)) {
    //         return $conflicts->isNotEmpty();
    //     }

    //     // Split the provided days into an array
    //     $newScheduleDays = explode('-', $days);

    //     // Check if any of the conflicting schedules occur on the same day
    //     foreach ($conflicts as $conflict) {
    //         $conflictDays = explode('-', $conflict->days);

    //         // Check for day conflicts
    //         foreach ($newScheduleDays as $newDay) {
    //             if (in_array($newDay, $conflictDays)) {
    //                 // Check if the section is the same
    //                 if ($conflict->section === $section) {
    //                     return true; // If conflict found
    //                 }
    //             }
    //         }
    //     }

    //     return false; // If no conflict found
    // }


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
