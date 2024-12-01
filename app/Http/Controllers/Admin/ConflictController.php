<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
    public function schedules(Request $request) {
        $query = Schedules::with(['teacher', 'subject', 'classroom'])->withTrashed();

        // Semester Filtering
        if ($request->has('semester') && $request->input('semester') !== '') {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('semester', $request->input('semester'));
            });
        }

        // Teacher Filtering (if needed)
        if ($request->has('teacher') && $request->input('teacher') !== '') {
            $query->where('teacher_id', $request->input('teacher'));
        }

        $schedules = $query->get();

        // Set is_conflicted attribute for each schedule
        foreach ($schedules as $schedule) {
            $schedule->is_conflicted = $schedule->hasConflict();
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

            // Normalize days input
            $days = $request->input('days');
            $days = is_string($days) ? explode(',', $days) : (is_array($days) ? $days : []);
            $days = array_filter(array_map('trim', $days));
            $daysString = implode('-', $days);

            // Attempt to parse times with multiple formats
            $parsedStartTime = $this->parseTime($startTime);
            $parsedEndTime = $this->parseTime($endTime);

            // Check for conflicts
            $hasConflict = $this->checkForConflicts(
                $request->teacher_id,
                $parsedStartTime,
                $parsedEndTime,
                $schedules->id, // Pass the current schedule ID to exclude it from conflict check
                $daysString,
                $request->input('section'),
                $request->input('room_id')
            );

            try {
                $parsedStartTime = $this->parseTime($startTime);
                $parsedEndTime = $this->parseTime($endTime);
            } catch (\Exception $e) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid time format: ' . $e->getMessage()
                ], 422);
            }

            // Validate request data with parsed times
            $validator = Validator::make([
                'teacher_id' => $request->input('teacher_id'),
                'semester' => $request->input('semester'),
                'categoryName' => $request->input('categoryName'),
                'days' => $days,
                'subject_id' => $request->input('subject_id'),
                'year' => $request->input('year'),
                'section' => $request->input('section'),
                'room_id' => $request->input('room_id'),
                'startTime' => $parsedStartTime,
                'endTime' => $parsedEndTime,
            ], [
                'teacher_id' => 'required|exists:teachers,id',
                'semester' => 'required|string',
                'categoryName' => 'required',
                'days' => 'required|array',
                'subject_id' => 'nullable|exists:subjects,id',
                'year' => 'required|in:Grade 11,Grade 12',
                'section' => 'required|string',
                'room_id' => 'required|exists:classroom,id',
                'startTime' => 'required|date_format:H:i:s',
                'endTime' => 'required|date_format:H:i:s|after:startTime',
            ], [
                'year.in' => 'Please select either Grade 11 or Grade 12.',
                'startTime.date_format' => 'Invalid start time format. Use HH:mm:ss',
                'endTime.date_format' => 'Invalid end time format. Use HH:mm:ss',
                'endTime.after' => 'End time must be after start time.',
            ]);

            // Check validation first
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Handle conflicts
            if ($hasConflict) {
                // Fetch additional details for conflict response
                $teacher = Teachers::findOrFail($request->input('teacher_id'));
                $subject = Subjects::findOrFail($request->input('subject_id', null));

                // Get available time slots
                $availableSlots = $this->getAvailableTimeSlots(
                    $request->teacher_id,
                    $days,
                    $parsedStartTime,
                    $parsedEndTime
                );

                // Return conflict response
                return response()->json([
                    'status' => 'conflict',
                    'message' => 'Schedule conflicts with existing schedules.',
                    'available_slots' => $availableSlots,
                    'original_schedule' => [
                        'id' => $schedules->id,
                        'teacher_id' => $teacher->id,
                        'teacher_name' => $teacher->teacherName,
                        'semester' => $request->input('semester'),
                        'category_name' => $request->input('categoryName'),
                        'subject_id' => $subject->id ?? null,
                        'subject_name' => $subject->subjectName ?? null,
                        'room_id' => $request->input('room_id'),
                        'year_section' => $request->input('year'),
                        'section' => $request->input('section'),
                        'days' => $daysString,
                        'start_time' => $parsedStartTime,
                        'end_time' => $parsedEndTime,
                    ]
                ], 409);
            }

            // Storing the old duration to adjust teacher's hours
            $oldStartTime = \Carbon\Carbon::parse($schedules->startTime);
            $oldEndTime = \Carbon\Carbon::parse($schedules->endTime);
            $oldDuration = $oldStartTime->diffInHours($oldEndTime, true);

            // Update the schedule
            $schedules->update([
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
            $newStartTime = \Carbon\Carbon::parse($schedules->startTime);
            $newEndTime = \Carbon\Carbon::parse($schedules->endTime);
            $newDuration = $newStartTime->diffInHours($newEndTime, true);

            $teacher = $schedules->teacher;
            if ($teacher) {
                // Adjust total hours by subtracting old duration and adding new duration
                $teacher->numberHours = max(0, $teacher->numberHours - $oldDuration + $newDuration);
                $teacher->save();
            }

            // Check for conflicts after updating the schedule
            $schedules->is_conflicted = $hasConflict;
            $schedules->save();

            // Determine the response type
            if ($request->expectsJson() || $request->ajax()) {
                // JSON response for AJAX/API requests
                return response()->json([
                    'status' => 'success',
                    'message' => 'Schedule updated successfully',
                    'schedule' => $schedules->load(['teacher', 'subject', 'classroom'])
                ], 200);
            } else {
                // Web response for traditional form submissions
                return redirect()->route('admin.schedules')
                    ->with('success', 'Schedule updated successfully');
            }

        } catch (\Exception $e) {
            \Log::error('Schedule update error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Return a JSON response with error details
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    // Function for creating schedules
    public function createSchedule(Request $request) {
        \Log::info('Create Schedule Request', [
            'all_data' => $request->all(),
            'method' => $request->method()
        ]);

        try {
            // Normalize days input
            $days = $request->input('days');
            $days = is_string($days) ? explode(',', $days) : (is_array($days) ? $days : []);
            $days = array_filter(array_map('trim', $days));

            if (empty($days)) {
                throw new \Exception("Please select at least one day.");
            }

            // Parse time in a uniform format
            $startTime = $request->input('startTime');
            $endTime = $request->input('endTime');

            $parsedStartTime = $this->parseTime($startTime);
            $parsedEndTime = $this->parseTime($endTime);

            // Prepare days string for database storage
            $daysString = implode('-', $days);

            // Validate request data
            $validator = Validator::make($request->all(), [
                'teacher_id' => 'required|exists:teachers,id',
                'semester' => 'required|string',
                'categoryName' => 'required',
                'subject_id' => 'nullable|exists:subjects,id',
                'year' => 'required|in:Grade 11,Grade 12',
                'section' => 'required|string',
                'room_id' => 'required|exists:classroom,id',
                'startTime' => 'required|date_format:H:i:s',
                'endTime' => 'required|date_format:H:i:s',
            ], [
                'year.in' => 'Please select either Grade 11 or Grade 12.',
            ]);

            // Check validation first
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Check for conflicts
            $hasConflict = $this->checkForConflicts(
                $request->teacher_id,
                $parsedStartTime,
                $parsedEndTime,
                null,
                $daysString,
                $request->input('section'),
                $request->input('room_id')
            );

            // Handle conflicts
            if ($hasConflict) {
                // Fetch additional details for conflict response
                $teacher = Teachers::findOrFail($request->input('teacher_id'));
                $subject = Subjects::findOrFail($request->input('subject_id', null));

                // Get available time slots
                $availableSlots = $this->getAvailableTimeSlots(
                    $request->teacher_id,
                    $days,
                    $parsedStartTime,
                    $parsedEndTime
                );

                // Return conflict response
                return response()->json([
                    'status' => 'conflict',
                    'message' => 'Schedule conflicts with existing schedules.',
                    'available_slots' => $availableSlots,
                    'original_schedule' => [
                        'teacher_id' => $teacher->id,
                        'teacher_name' => $teacher->teacherName,
                        'semester' => $request->input('semester'),
                        'category_name' => $request->input('categoryName'),
                        'subject_id' => $subject->id ?? null,
                        'subject_name' => $subject->subjectName ?? null,
                        'room_id' => $request->input('room_id'),
                        'year_section' => $request->input('year'),
                        'section' => $request->input('section'),
                        'days' => $daysString,
                        'start_time' => $parsedStartTime,
                        'end_time' => $parsedEndTime,
                    ]
                ], 409);
            }

            // Create schedule
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

            // Update teacher's hours
            $schedule->calculateAndUpdateTeacherHours();

            // Determine the response type
            if ($request->expectsJson() || $request->ajax()) {
                // JSON response for AJAX/API requests
                return response()->json([
                    'status' => 'success',
                    'message' => 'Schedule created successfully',
                    'schedule' => $schedule->load(['teacher', 'subject', 'classroom'])
                ], 201);
            } else {
                return redirect()->route('admin.schedules')
                    ->with('success', 'Schedule created successfully');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 'error',
                'errors' => $e->validator->errors()
            ], 422);

        } catch (\Exception $e) {
            // Detailed error logging
            \Log::error('Schedule Creation Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            // Return a JSON response with error details
            return response()->json([
                'status' => 'error',
                'message' => 'An unexpected error occurred: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
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
            'H:i:s',     // 24-hour with seconds
            'H:i',       // 24-hour without seconds
            'h:i A',     // 12-hour with AM/PM
            'h:i:s A',   // 12-hour with seconds and AM/PM
            'h:i a',     // Lowercase am/pm
            'H:i a'      // 24-hour with lowercase am/pm
        ];

        // Try parsing with different formats
        foreach ($formats as $format) {
            try {
                $parsedTime = Carbon::createFromFormat($format, $time);
                return $parsedTime->format('H:i:s');
            } catch (\Exception $e) {
                continue;
            }
        }

        // If no format works, throw an exception with details
        throw new \Exception("Unable to parse time: {$time}. Supported formats: " . implode(', ', $formats));
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

    /*
    // Primary conflict check method for teacher's time and day conflicts
    private function checkTimeAndDayConflict($teacherId, $startTime, $endTime, $days) {
        // Convert days to array if needed
        $newScheduleDays = is_array($days) ? $days : explode('-', $days);

        // Check for time and day overlap for the same teacher
        return Schedules::where('teacher_id', $teacherId)
            // Time overlap check
            ->where(function($query) use ($startTime, $endTime) {
                $query->where('startTime', '<', $endTime)
                    ->where('endTime', '>', $startTime);
            })
            // Day overlap check
            ->where(function($dayQuery) use ($newScheduleDays) {
                foreach ($newScheduleDays as $day) {
                    $dayQuery->orWhereRaw("FIND_IN_SET(?, REPLACE(days, '-', ',')) > 0", [$day]);
                }
            })
            ->exists();
    }

    // Separate method for same section conflict
    private function checkSameSectionConflict($teacherId, $section, $startTime, $endTime, $days) {
        // Convert days to array if needed
        $newScheduleDays = is_array($days) ? $days : explode('-', $days);

        // Check for conflicts in the same section
        return Schedules::where('section', $section)
            // Time overlap check
            ->where(function($query) use ($startTime, $endTime) {
                $query->where('startTime', '<', $endTime)
                    ->where('endTime', '>', $startTime);
            })
            // Day overlap check
            ->where(function($dayQuery) use ($newScheduleDays) {
                foreach ($newScheduleDays as $day) {
                    $dayQuery->orWhereRaw("FIND_IN_SET(?, REPLACE(days, '-', ',')) > 0", [$day]);
                }
            })
            // Exclude the current teacher
            ->where('teacher_id', '!=', $teacherId)
            ->exists();
    }

    // Modify your existing checkForConflicts method to use these new methods
    private function checkForConflicts($teacherId, $startTime, $endTime, $exceptId = null, $days = null, $section = null, $roomId = null) {
        // Check for teacher's own time and day conflicts
        $teacherConflict = $this->checkTimeAndDayConflict($teacherId, $startTime, $endTime, $days);
        if ($teacherConflict) {
            \Log::info('Teacher Time and Day Conflict', [
                'teacher_id' => $teacherId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'days' => $days
            ]);
            return true;
        }

        // Check for same section conflicts with other teachers
        if ($section) {
            $sectionConflict = $this->checkSameSectionConflict($teacherId, $section, $startTime, $endTime, $days);
            if ($sectionConflict) {
                \Log::info('Section Conflict', [
                    'teacher_id' => $teacherId,
                    'section' => $section,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'days' => $days
                ]);
                return true;
            }
        }

        // Optional: Room conflict check
        if ($roomId) {
            $roomConflict = Schedules::where('room_id', $roomId)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where('startTime', '<', $endTime)
                        ->where('endTime', '>', $startTime);
                })
                ->where(function($dayQuery) use ($days) {
                    $newScheduleDays = is_array($days) ? $days : explode('-', $days);
                    foreach ($newScheduleDays as $day) {
                        $dayQuery->orWhereRaw("FIND_IN_SET(?, REPLACE(days, '-', ',')) > 0", [$day]);
                    }
                })
                ->exists();

            if ($roomConflict) {
                \Log::info('Room Conflict', [
                    'room_id' => $roomId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'days' => $days
                ]);
                return true;
            }
        }

        return false;
    }
    */

    private function checkTimeAndDayConflict($teacherId, $startTime, $endTime, $days, $exceptId = null) {
        // Convert days to array if needed
        $newScheduleDays = is_array($days) ? $days : explode('-', $days);

        // Check for time and day overlap for the same teacher
        $query = Schedules::where('teacher_id', $teacherId)
            // Time overlap check
            ->where(function($query) use ($startTime, $endTime) {
                $query->where('startTime', '<', $endTime)
                    ->where('endTime', '>', $startTime);
            })
            // Day overlap check
            ->where(function($dayQuery) use ($newScheduleDays) {
                foreach ($newScheduleDays as $day) {
                    $dayQuery->orWhereRaw("FIND_IN_SET(?, REPLACE(days, '-', ',')) > 0", [$day]);
                }
            });

        // Exclude the current schedule if an ID is provided
        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    private function checkSameSectionConflict($teacherId, $section, $startTime, $endTime, $days, $exceptId = null) {
        // Convert days to array if needed
        $newScheduleDays = is_array($days) ? $days : explode('-', $days);

        // Check for conflicts in the same section
        $query = Schedules::where('section', $section)
            // Time overlap check
            ->where(function($query) use ($startTime, $endTime) {
                $query->where('startTime', '<', $endTime)
                    ->where('endTime', '>', $startTime);
            })
            // Day overlap check
            ->where(function($dayQuery) use ($newScheduleDays) {
                foreach ($newScheduleDays as $day) {
                    $dayQuery->orWhereRaw("FIND_IN_SET(?, REPLACE(days, '-', ',')) > 0", [$day]);
                }
            })
            // Exclude the current teacher
            ->where('teacher_id', '!=', $teacherId);

        // Exclude the current schedule if an ID is provided
        if ($exceptId !== null) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->exists();
    }

    private function checkForConflicts($teacherId, $startTime, $endTime, $exceptId = null, $days = null, $section = null, $roomId = null) {
        // Check for teacher's own time and day conflicts
        $teacherConflict = $this->checkTimeAndDayConflict($teacherId, $startTime, $endTime, $days, $exceptId);
        if ($teacherConflict) {
            \Log::info('Teacher Time and Day Conflict', [
                'teacher_id' => $teacherId,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'days' => $days
            ]);
            return true;
        }

        // Check for same section conflicts with other teachers
        if ($section) {
            $sectionConflict = $this->checkSameSectionConflict($teacherId, $section, $startTime, $endTime, $days, $exceptId);
            if ($sectionConflict) {
                \Log::info('Section Conflict', [
                    'teacher_id' => $teacherId,
                    'section' => $section,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'days' => $days
                ]);
                return true;
            }
        }

        // Optional: Room conflict check
        if ($roomId) {
            $roomConflict = Schedules::where('room_id', $roomId)
                ->where(function($query) use ($startTime, $endTime) {
                    $query->where('startTime', '<', $endTime)
                        ->where('endTime', '>', $startTime);
                })
                ->where(function($dayQuery) use ($days) {
                    $newScheduleDays = is_array($days) ? $days : explode('-', $days);
                    foreach ($newScheduleDays as $day) {
                        $dayQuery->orWhereRaw("FIND_IN_SET(?, REPLACE(days, '-', ',')) > 0", [$day]);
                    }
                })
                // Exclude the current schedule if an ID is provided
                ->when($exceptId !== null, function($query) use ($exceptId) {
                    return $query->where('id', '!=', $exceptId);
                })
                ->exists();

            if ($roomConflict) {
                \Log::info('Room Conflict', [
                    'room_id' => $roomId,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'days' => $days
                ]);
                return true;
            }
        }

        return false;
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
