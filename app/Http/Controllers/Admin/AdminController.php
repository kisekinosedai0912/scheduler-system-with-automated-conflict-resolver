<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Events;
use App\Models\Schedules;
use Illuminate\Http\Request;
use App\Models\Subjects;
use App\Models\Teachers;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Function for returning view in the home page of the admin dashboard
    public function adminIndex() {
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
        return view('admin.home', ['events' => $events, 'prevEventStartTime' => $prevEventStartTime]);
    }

    // Function for creating events
    public function createEvent(Request $request) {
        // Trial and error logic before creating an event in the calendar
        try {
            // Validating the incoming value of the form elements before querying to creation
            $request->validate([
                'title' => 'required|string',
                'start' => 'required|date_format:Y-m-d\TH:i:s',
                'end' => 'required|date_format:Y-m-d\TH:i:s|after_or_equal:start',
                'startTime' => 'required|date_format:H:i',
                'endTime' => 'required|date_format:H:i|after:startTime',
            ]);

            $startDateTime = Carbon::parse($request->input('start')); // Parse the start date and time before creation
            $endDateTime = Carbon::parse($request->input('end')); // Parse the end date and time before creation

            $event = Events::create([
                'eventTitle' => $request->input('title'),
                'startDate' => $startDateTime->toDateString(), // Convert to string before storing in the database table
                'endDate' => $endDateTime->toDateString(),
                'startTime' => $startDateTime->toTimeString(),
                'endTime' => $endDateTime->toTimeString(),
            ]);

            // Create notifications for all faculty members
            $facultyMembers = User::where('user_role', 'faculty')->get();
            foreach ($facultyMembers as $faculty) {
                Notifications::create([
                    'event_id' => $event->id,
                    'user_id' => $faculty->id,
                    'is_read' => false,
                ]);
            }

            return response()->json(['success' => 'Event created successfully']); // Return a json response and display a success message if successful
        } catch (\Exception $e) {
            \Log::error('Event creation error: ' . $e->getMessage());
            // Return a json response for the exception with an error message
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }

    // Function for resizing events in the calendar
    public function resizeEvent(Request $request, $id) {
        // Requests a validation to the server for resizing an event
        $request->validate([
            'endDate' => 'required|date_format:Y-m-d',
            'endTime' => 'required|date_format:H:i|after:startTime',
        ]);

        $event = Events::findOrFail($id); // Find the ID
        // Parse the time and date
        $newEndDateTime = Carbon::parse($request->input('endDate') . ' ' . $request->input('endTime'))->setTimezone('UTC');

        $event->update([
            'endDate' => $newEndDateTime->toDateString(), // Convert to string
            'endTime' => $newEndDateTime->toTimeString(),
        ]);

        return response()->json(['message' => 'Event resized']);
    }

    // Function for dragging and dropping of events
    public function dragEvent(Request $request, $id) {
        // Find the ID of the event in the calendar
        $calendarEvents = Events::find($id);
        // CHecks if the event is present, if not, display an error
        if (!$calendarEvents) {
            return response()->json(['error' => 'Unable to locate event'], 404);
        }
        // Logic to update event dates after a successfull drag change
        try {
            $calendarEvents->update([
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage()); // Redirect back with an excption error message
        }
        return response()->json('Event updated successfully'); // Return a json response if successfull
    }

    // Function for deleting events in the calendar
    public function deleteEvent($id) {
        // Find the id of the event to delete and request a delete in the server
        try {
            $event = Events::findOrFail($id);
            $event->delete();
        } catch (\Exception $e) {
            // Redirect  back to the page with an exception message
            return redirect()->route('admin.home')->with('error', 'Error occurred while deleting the event: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Event deleted successfully!']); // Return a json response after successful deletion with a success emssage
    }

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

        try {
            $schedule = Schedules::create([
                'teacher_id' => $request->input('teacher_id'),
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

            return redirect()->route('admin.schedules')->with('success', 'Schedule added successfully!');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // Function to check for schedule conflicts
    private function checkForConflicts($teacherId, $startTime, $endTime, $exceptId = null) {
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

    // Function for returning view in the schedule page of the admin dashboard
    public function subjects(Request $request) {
        $query = Subjects::query();

        if ($request->has('searchSubject')) {
            $search = $request->input('searchSubject');
            $query->where('subjectName', 'LIKE', "%{$search}%");
        }

        $paginateSubjects = $query->paginate(7);

        return view('admin.subjects', compact('paginateSubjects')); // The same as the previous ones
    }

    // Function for creating subjects in the database
    public function createSubject(Request $request) {
        $data = $request->validate([
            'category' => 'required|string',
            'subjectName' => 'required|string',
            'description' => 'nullable|string'
        ]);

        try {
            Subjects::create($data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.subjects')->with('success', 'Subject added successfully!');
    }

    // Function for showing modal form for editing of fields in the table
    public function editSubject(Subjects $subject) {
        return view('admin-modals.editSubject', ['subject' => $subject]);
    }

    // Function for updating the specific subject in the table
    public function updateSubject(Request $request, Subjects $subject)
    {
        $data = $request->validate([
            'category' => 'required|string',
            'subjectName' => 'required|string',
            'description' => 'nullable|string',
        ]);

        try {
            $subject->update($data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Subject updated successfully!');
    }

    // Function for deleting the specific id of the subject in the database
    public function deleteSubject($id)
    {
        $subject = Subjects::find($id);

        if ($subject)
            $subject->delete();

        else
            return redirect()->route('admin.subjects')->with('error', 'Subject not found.');

        return redirect()->route('admin.subjects')->with('success', 'Subject deleted successfully.');
    }

    // Function for returning view in the teacher loads page of the admin dashboard
    public function teacher(Request $request) {

        $query = Teachers::query();

        if ($request->has('searchTeacher')) {
            $search = $request->input('searchTeacher');
            $query->where('teacherName', 'LIKE', "%{$search}%");
        }
        $paginateLoads = $query->paginate(7);

        return view('admin.teacher', compact('paginateLoads',));
    }

    // Function for the api route to get the data in the subjects table and populate the subject dropdown with the subjects based on the selected category
    public function getCategory($categoryId){
        $subjects = Subjects::where('category', $categoryId)->distinct()->get();

        return response()->json($subjects);
    }

    // Function for adding new teacher loads
    public function createLoad(Request $request) {
        $loadData = $request->validate([
            'teacherName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers,email',
            'contact' => 'required|string|max:15',
            //'numberHours' => 'required|integer|min:1',
        ]);

        try {
            // Create a new teacher record
            Teachers::create($loadData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->route('admin.teacher')->with('success', 'Teacher added successfully!');
    }

    // Function for editing teacher loads
    public function editLoad(Teachers $teachers) {
        return view('admin-modals.editTeacher', ['teachers' => $teachers]);
    }

    // Function for updating teacher loads
    public function updateLoad(Request $request, $id) {
        $request->validate([
            'teacherName' => 'required|string',
            'email' => 'required|string',
            'contact' => 'required|string',
            'numberHours' => 'required|integer'
        ]);

        try {
            $teacher = Teachers::findOrFail($id);
            $teacher->update([
                'teacherName' => $request->input('teacherName'),
                'email' => $request->input('email'),
                //'subject_id' => Subjects::where('subjectName', $request->input('subjectName'))->first()->id,
                'contact' => $request->input('contact'),
                'numberHours' => $request->input('numberHours')
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Load updated successfully!');
    }

    // Function for load deletion in the database
    public function deleteLoad($id) {
        try {
            $loadData =Teachers::findOrFail($id);
            $loadData->delete();

            return redirect()->route('admin.teacher')->with('success', 'Load deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.teacher')->with('error', 'Error: ' . $e->getMessage());

        } catch (\Exception $e) {
            return redirect()->route('admin.teacher')->with('error', 'Error occurred while deleting the record: ' . $e->getMessage());
        }
    }

    // Function for returning view in the classroom page
    public function classroom(Request $request) {
        $query = Classroom::query();

        if ($request->has('searchRoom')) {
            $search = $request->input('searchRoom');
            $query->where('classroomNumber', 'LIKE', "%{$search}%")
                ->orWhere('buildingNumber', 'LIKE', "%{$search}%");
        }

        $paginateRooms = $query->paginate(7);

        return view('admin.classroom', compact('paginateRooms'));
    }

    // Function for creating rooms in the database
    public function createRoom(Request $request) {
        $roomData = $request->validate([
            'roomName' => 'required|string',
            'buildingNumber' => 'nullable|string',
            'floorNumber' => 'nullable|string'
        ]);

        try {
            Classroom::create($roomData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
        return redirect()->route('admin.classroom')->with('success', 'Classroom added successfully!');
    }

    // Function for viewing the edit modal
    public function editRoom(Classroom $classroom) {
        return view('admin-modals.editClassroom', ['classroom' => $classroom]);
    }

    // Function for updating the classroom details
    public function updateRoom(Request $request, Classroom $classroom) {
        $data = $request->validate([
            'roomName' => 'required|string',
            'buildingNumber' => 'nullable|string',
            'floorNumber' => 'nullable|string'
        ]);

        try {
            $classroom->update($data);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Classroom updated successfully!');
    }

    // Function for deleting classroom
    public function deleteRoom($id) {
        try {
            $classroom = Classroom::findOrFail($id);
            $classroom->delete();

            return redirect()->route('admin.classroom')->with('success', 'Record deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.classroom')->with('error', 'Error: ' . $e->getMessage());

        } catch (\Exception $e) {
            return redirect()->route('admin.classroom')->with('error', 'Error occurred while deleting the record: ' . $e->getMessage());
        }
    }

    // Function to return view to the users page
    public function accounts(Request $request) {
        $query = User::query(); // Preparing a query builder to search users on the Users model
        $teachers = Teachers::doesntHave('users')->get();
        // CHecks for a search parameter to do searching if it exists
        if ($request->has('search')) {
            // Retrieve users based from query result either by name or email search
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        }

        $users = $query->paginate(7); // Paginate the users table while also paginating the search results to lessen run time and load time from the server

        return view('admin.users', compact('users', 'teachers')); // Returns to users page with the users key that has a value of $users
    }

    // Function for returning view to edit user modal
    public function edit_user(User $users) {
        return view('admin-modals.editUser', ['users' => $users]); // Returns similarly to the code above that uses compact method
    }

    // Function for updating user credentials
    public function update_user(Request $request, User $user) {
        // Validate user request to patch the existing credentials (Exception: passwords, names and email updates are done by the user itself on its account)
        $userData = $request->validate([
            'name' => 'required|string|max:255',
            'user_role' => 'required|string|in:faculty,admin'
        ]);
        // Trial and error logic to update data while maintaining view to possible errors and return thos errors to be handled and displayed
        try {
            $user->update($userData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    // Function for deleting user in the users database
    public function delete_user($id) {
        // Trial and error logic handling before deletion of existing credentials
        try {
            $user = User::findOrFail($id);
            $user->delete();
            // Redirect back to the page after a successful deletion with a success message confirmation
            return redirect()->route('admin.users')->with('success', 'Record deleted successfully.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Redirect back if in case of model not accessible or exists in the database
            return redirect()->route('admin.users')->with('error', 'Error: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Redirect back if an error occurs and display the error
            return redirect()->route('admin.users')->with('error', 'Error occurred while deleting the record: ' . $e->getMessage());
        }
    }
}
