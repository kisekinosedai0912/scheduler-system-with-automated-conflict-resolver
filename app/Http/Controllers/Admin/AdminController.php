<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Events;
use App\Models\Schedules;
use Illuminate\Http\Request;
use App\Models\Subjects;
use App\Models\Teachers;
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
            $request->validate([
                'title' => 'required|string',
                'start' => 'required|date_format:Y-m-d\TH:i:s',
                'end' => 'required|date_format:Y-m-d\TH:i:s|after_or_equal:start',
                'startTime' => 'required|date_format:H:i',
                'endTime' => 'required|date_format:H:i|after:startTime',
            ]);
     
            $startDateTime = Carbon::parse($request->input('start')); // Parse the start date and time before creation
            $endDateTime = Carbon::parse($request->input('end')); // Parse the end date and time before creation
     
            Events::create([
                'eventTitle' => $request->input('title'),
                'startDate' => $startDateTime->toDateString(), // Convert to string before storing in the database table
                'endDate' => $endDateTime->toDateString(),
                'startTime' => $startDateTime->toTimeString(),
                'endTime' => $endDateTime->toTimeString(),
            ]);
     
            return response()->json(['success' => 'Event created successfully']); // Return a json response and display a success message if successful
        } catch (\Exception $e) {
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
    
        return response()->json(['message' => 'Event resized']); // Return a json response of the resized data
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
        $schedules = Schedules::all(); // Retrieve all of the data in the schedules table model
        return view('admin.schedules', ['schedules' => $schedules]); // Returns a key $schedules and redirects to the schedules page
    }
    // Function for editing schedules
    public function editSchedule(Schedules $schedules) {
        return view('admin.schedules', ['schedules' => $schedules]);
    }
    // Function for updating schedule
    public function updateSchedule(Request $request, Schedules $schedules){
        // Convert the start and end time into a 12-hour format and requests an update of the data with formatted times before storing in the database table 
        $request->merge([
            'startTime' => Carbon::createFromFormat('h:i A', $request->startTime)->format('H:i:s'),
            'endTime' => Carbon::createFromFormat('h:i A', $request->endTime)->format('H:i:s'),
        ]);
        // Validate the update request
        $scheduleData = $request->validate([
            'teacherName' => 'required|string',
            'subject' => 'required|string',
            'studentNum' => 'required|integer',
            'yearSection' => 'required|string',
            'room' => 'required|string',
            'startTime' => 'required|date_format:H:i:s',
            'endTime' => 'required|date_format:H:i:s',
        ]);
        // Logic for handling a success and error cases
        try {
            $schedules->update($scheduleData);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    
        return redirect()->back()->with('success', 'Schedule updated successfully!');
    }
    // Function for creating schedules
    public function createSchedule(Request $request) {
        // The same as  the code above
        $request->merge([
            'startTime' => Carbon::createFromFormat('h:i A', $request->startTime)->format('H:i:s'),
            'endTime' => Carbon::createFromFormat('h:i A', $request->endTime)->format('H:i:s'),
        ]);
        // Validate the create request
        $scheduleData = $request->validate([
            'teacherName' => 'required|string',
            'subject' => 'required|string',
            'studentNum' => 'required|integer',
            'yearSection' => 'required|string',
            'room' => 'required|string',
            'startTime' => 'required|date_format:H:i:s', 
            'endTime' => 'required|date_format:H:i:s',   
        ]);
    
        try {
            Schedules::create($scheduleData);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    
        return redirect()->route('admin.schedules')->with('success', 'Schedule added successfully!');
    }
    // Function for deletion of schedules
    public function deleteSchedule($id) {
        // The same thing as for the other deletion functions
        try {
            $scheduleData = Schedules::findOrFail($id);
            $scheduleData->delete();

            return redirect()->route('admin.schedules')->with('success', 'Schedule deleted successfully.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.schedules')->with('error', 'Error: ' . $e->getMessage());
            
        } catch (\Exception $e) {
            return redirect()->route('admin.schedules')->with('error', 'Error occurred while deleting the record: ' . $e->getMessage());
        }
    }
    // Function for returning view in the schedule page of the admin dashboard
    public function subjects() {
        $paginateSubjects = Subjects::paginate(7); // Paginates the table list with a limit of 7 datasets per page
        $subjects = Subjects::all(); // Retrieve all subjects in the database table subjects

        return view('admin.subjects', compact('paginateSubjects', 'subjects')); // The same as the previous ones
    }
    // Function for creating subjects in the database
    public function createSubject(Request $request) {
        // The same validation rule before creation of data in the database tables
        $data = $request->validate([
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
    public function teacher() {
        $paginateLoads = Teachers::with('subject')->paginate(7); 
        $subjects = Subjects::all(); 
    
        return view('admin.teacher', compact('paginateLoads', 'subjects')); 
    }
    // Function for adding new teacher loads
    public function createLoad(Request $request) {
        $loadData = $request->validate([
            'teacherName' => 'required|string',
            'subjectName' => 'required|string',
            'numberHours' => 'required|integer'
        ]);
    
        try {
            Teachers::create($loadData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
        return redirect()->route('admin.teacher')->with('success', 'Load added successfully!');
    }
    // Function for editing teacher loads
    public function editLoad(Teachers $teachers) {
        return view('admin-modals.editTeacher', ['teachers' => $teachers]);
    }
    // Function for updating teacher loads
    public function updateLoad(Request $request, $id) {
        $loadData = $request->validate([
            'teacherName' => 'required|string',
            'subjectName' => 'required|string',
            'numberHours' => 'required|integer'
        ]);
    
        try {
            $teacher = Teachers::findOrFail($id);
            $teacher->update($loadData);
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
    public function classroom() {
        $rooms = Classroom::all();
        $paginateRooms = Classroom::paginate(7);

        return view('admin.classroom', compact(['rooms', 'paginateRooms']));
    }
    // Function for creating rooms in the database 
    public function createRoom(Request $request) {
        $roomData = $request->validate([
            'classroomNumber' => 'required|string',
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
            'classroomNumber' => 'required|string',
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
        // CHecks for a search parameter to do searching if it exists 
        if ($request->has('search')) {
            // Retrieve users based from query result either by name or email search
            $search = $request->input('search');
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('email', 'LIKE', "%{$search}%");
        }

        $users = $query->paginate(7); // Paginate the users table while also paginating the search results to lessen run time and load time from the server

        return view('admin.users', compact('users')); // Returns to users page with the users key that has a value of $users 
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
