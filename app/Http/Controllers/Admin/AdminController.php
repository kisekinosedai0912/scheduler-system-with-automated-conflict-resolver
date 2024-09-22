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
        $events = array();
        $calendarEvents = Events::all();
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
        try {
            $request->validate([
                'title' => 'required|string',
                'start' => 'required|date_format:Y-m-d\TH:i:s',
                'end' => 'required|date_format:Y-m-d\TH:i:s|after_or_equal:start',
                'startTime' => 'required|date_format:H:i',
                'endTime' => 'required|date_format:H:i|after:startTime',
            ]);
     
            $startDateTime = Carbon::parse($request->input('start'));
            $endDateTime = Carbon::parse($request->input('end'));
     
            Events::create([
                'eventTitle' => $request->input('title'),
                'startDate' => $startDateTime->toDateString(),
                'endDate' => $endDateTime->toDateString(),
                'startTime' => $startDateTime->toTimeString(),
                'endTime' => $endDateTime->toTimeString(),
            ]);
     
            return response()->json(['success' => 'Event created successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
    }
    // Function for resizing events in the calendar
    public function resizeEvent(Request $request, $id) {
        $request->validate([
            'endDate' => 'required|date_format:Y-m-d',
            'endTime' => 'required|date_format:H:i|after:startTime', 
        ]);
    
        $event = Events::findOrFail($id);

        $newEndDateTime = Carbon::parse($request->input('endDate') . ' ' . $request->input('endTime'))->setTimezone('UTC');
    
        $event->update([
            'endDate' => $newEndDateTime->toDateString(),
            'endTime' => $newEndDateTime->toTimeString(),
        ]);
    
        return response()->json(['message' => 'Event resized']);
    }
    // Function for dragging and dropping of events
    public function dragEvent(Request $request, $id) {
        $calendarEvents = Events::find($id);
        if (!$calendarEvents) {
            return response()->json(['error' => 'Unable to locate event'], 404);
        }
        try {
            $calendarEvents->update([
                'startDate' => $request->startDate,
                'endDate' => $request->endDate,
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
        return response()->json('Event updated successfully');
    }
    // Function for deleting events in the calendar
    public function deleteEvent($id) {
        try {
            $event = Events::findOrFail($id);
            $event->delete();
        } catch (\Exception $e) {
            return redirect()->route('admin.home')->with('error', 'Error occurred while deleting the event: ' . $e->getMessage());
        }

        return response()->json(['message' => 'Event deleted successfully!']);
    }
    // Function for returning view in the schedule page of the admin dashboard
    public function schedules() {
        $schedules = Schedules::all();
        return view('admin.schedules', ['schedules' => $schedules]);
    }
    // Function for editing schedules
    public function editSchedule(Schedules $schedules) {
        return view('admin.schedules', ['schedules' => $schedules]);
    }
    // Function for updating schedule
    public function updateSchedule(Request $request, Schedules $schedules){
        $request->merge([
            'startTime' => Carbon::createFromFormat('h:i A', $request->startTime)->format('H:i:s'),
            'endTime' => Carbon::createFromFormat('h:i A', $request->endTime)->format('H:i:s'),
        ]);

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
            $schedules->update($scheduleData);

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
        $paginateSubjects = Subjects::paginate(7); 
        $subjects = Subjects::all();
        return view('admin.subjects', compact('paginateSubjects', 'subjects'));
    }
    
    // Function for creating subjects in the database
    public function createSubject(Request $request) {
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
        $search = $request->input('search');
        $query = User::query();
    
        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
        }
    
        $paginateUsers = $query->paginate(7);
        
        if ($request->ajax()) {
            return view('admin.partials.user_table', compact('paginateUsers'));
        }
    
        return view('admin.users', compact('paginateUsers', 'search'));
    }
    // Function for returning view to edit user modal
    public function edit_user(User $users) {
        return view('admin-modals.editUser', ['users' => $users]);
    }
    // Function for updating user credentials
    public function update_user(Request $request, User $user) {
        $userData = $request->validate([
            'name' => 'required|string|max:255',
            'user_role' => 'required|string|in:faculty,admin'
        ]);        
    
        try {
            $user->update($userData);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }        
    
        return redirect()->back()->with('success', 'User updated successfully!');
    }
    // Function for deleting user in the users database
    public function delete_user($id) {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return redirect()->route('admin.users')->with('success', 'Record deleted successfully.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.users')->with('error', 'Error: ' . $e->getMessage());
            
        } catch (\Exception $e) {
            return redirect()->route('admin.users')->with('error', 'Error occurred while deleting the record: ' . $e->getMessage());
        }
    }
}
