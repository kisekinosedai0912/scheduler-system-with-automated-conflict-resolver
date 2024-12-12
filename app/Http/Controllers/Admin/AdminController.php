<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Events;
use App\Models\Schedules;
use App\Models\Subjects;
use App\Models\Teachers;
use App\Models\Notifications;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

use App\Services\SMSService;
use Infobip\Api\SmsApi;
use Illuminate\Support\Facades\Log;
use Infobip\Configuration;
use Infobip\ApiException;
use Infobip\Model\SmsAdvancedTextualRequest;
use Infobip\Model\SmsDestination;
use Infobip\Model\SmsTextualMessage;


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

            $event = Events::create([
                'eventTitle' => $request->input('title'),
                'startDate' => $startDateTime->toDateString(),
                'endDate' => $endDateTime->toDateString(),
                'startTime' => $startDateTime->toTimeString(),
                'endTime' => $endDateTime->toTimeString(),
            ]);

            // Create notifications for faculty members
            $facultyMembers = User::where('user_role', 'faculty')->get();
            $notifications = [];
            foreach ($facultyMembers as $faculty) {
                $notifications[] = [
                    'event_id' => $event->id,
                    'user_id' => $faculty->id,
                    'is_read' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Bulk insert notifications
            if (!empty($notifications)) {
                Notifications::insert($notifications);
            }

            // SMS Configuration
            $baseUrl = config('services.infobip.base_url');
            $apiKey = config('services.infobip.api_key');
            $senderId = config('services.infobip.sender_id', 'SCSHSAdmin');

            // Prepare SMS Configuration
            $configuration = new Configuration(
                host: $baseUrl,
                apiKey: $apiKey
            );

            // Initialize SMS API
            $sendSmsApi = new SmsApi(config: $configuration);

            // Fetch all contact numbers from the Teachers model
            $contactNum = Teachers::whereNotNull('contact')
                ->pluck('contact')
                ->filter(function($number) {
                    // Validate and format phone numbers
                    return $this->formatPhoneNumber($number);
                })
                ->toArray();

            // Prepare message
            $messageText = "A new event '{$event->eventTitle}' has been scheduled from {$startDateTime->toDateTimeString()} to {$endDateTime->toDateTimeString()}.";

            // Prepare destinations for SMS
            $destinations = array_map(function($number) {
                return new SmsDestination(to: $number);
            }, $contactNum);

            // Check if we have any valid destinations
            if (empty($destinations)) {
                Log::warning('No valid phone numbers found for SMS');
                return redirect()->route('admin.schedules')->with('warning', 'Event created but no SMS sent due to invalid phone numbers');
            }

            // Create SMS message
            $message = new SmsTextualMessage(
                destinations: $destinations,
                from: $senderId,
                text: $messageText
            );

            $smsRequest = new SmsAdvancedTextualRequest(messages: [$message]);

            try {
                // Send SMS
                $smsResponse = $sendSmsApi->sendSmsMessage($smsRequest);

                // Log successful SMS sending
                Log::info('SMS Sent Successfully', [
                    'event_id' => $event->id,
                    'event' => $event->eventTitle,
                    'destinations' => $contactNum
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Event created and SMS sent successfully',
                    'event' => $event
                ]);

            } catch (ApiException $apiException) {
                // Log SMS sending failure
                Log::error('SMS Sending Failed', [
                    'event_id' => $event->id,
                    'error_message' => $apiException->getMessage(),
                    'error_code' => $apiException->getCode()
                ]);

                return redirect()->route('admin.schedules')->with('warning', 'Event created but SMS sending failed: ' . $apiException->getMessage());
            }

        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Event creation error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }

    private function formatPhoneNumber($number) {
        // Remove any non-digit characters
        $phone = preg_replace('/\D/', '', $number);

        // Ensure the number starts with 63 and is 12 digits long
        if (strpos($phone, '63') === 0 && strlen($phone) === 12) {
            return '+' . $phone;
        }

        // If number starts with 09, convert to +63
        if (strpos($phone, '09') === 0) {
            $phone = '63' . substr($phone, 2);
            if (strlen($phone) === 12) {
                return '+' . $phone;
            }
        }

        // Log invalid phone number
        Log::warning('Invalid Phone Number', ['number' => $number]);

        return null;
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
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
        return response()->json('Event updated successfully');
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
    public function subjects(Request $request) {
        $query = Subjects::query();

        if ($request->has('searchSubject')) {
            $search = $request->input('searchSubject');
            $query->where('subjectName', 'LIKE', "%{$search}%");
        }

        if ($request->has('sort_by_strand') && $request->input('sort_by_strand') !== '') {
            $query->where('strand', $request->input('sort_by_strand'));
        }

        $paginateSubjects = $query->paginate(7);
        $uniqueStrands = Subjects::distinct('strand')->pluck('strand');

        return view('admin.subjects', compact('paginateSubjects', 'uniqueStrands'));
    }

    // Function for creating subjects in the database
    public function createSubject(Request $request) {
        $validator = Validator::make($request->all(), [
            'semester' => 'required|string',
            'track' => 'required|string',
            'strand' => 'required|string',
            'specialization' => 'nullable|string|max:255',
            'category' => 'required|string',
            'subjectName' => [
                'required',
                'string',
                Rule::unique('subjects', 'subjectName')
                    ->where('semester', $request->input('semester'))
                    ->where('specialization', $request->input('specialization'))
            ],
            'description' => 'nullable|string|max:500'
        ]);

        // Conditional validation for TVL Track
        if ($request->input('track') === 'TVL Track') {
            $validator->sometimes('specialization', 'required|string|max:255', function () {
                return true;
            });
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $subject = Subjects::create($validator->validated());

            return redirect()->route('admin.subjects')->with('success', 'Subject added successfully!');
        } catch (\Exception $e) {
            \Log::error('Subject Creation Error: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred');
        }
    }

    // Function for showing modal form for editing of fields in the table
    public function editSubject(Subjects $subject) {
        return view('admin-modals.editSubject', ['subject' => $subject]);
    }

    // Function for updating the specific subject in the table
    public function updateSubject(Request $request, Subjects $subject) {
        $validator = Validator::make($request->all(), [
            'semester' => 'required|string',
            'track' => 'required|string',
            'strand' => 'required|string',
            'specialization' => 'nullable|string|max:255',
            'category' => 'required|string',
            'subjectName' => [
                'required',
                'string',
                Rule::unique('subjects', 'subjectName')
                    ->where('semester', $request->input('semester'))
                    ->where('specialization', $request->input('specialization'))
                    ->ignore($subject->id)  // Ignore the current subject's ID
            ],
            'description' => 'nullable|string|max:500'
        ]);

        // Conditional validation for TVL Track
        if ($request->input('track') === 'TVL Track') {
            $validator->sometimes('specialization', 'required|string|max:255', function () {
                return true;
            });
        }

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $subject->update($validator->validated());

            return redirect()->route('admin.subjects')->with('success', 'Subject updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Subject Update Error: ' . $e->getMessage());
            return back()->with('error', 'An unexpected error occurred during update');
        }
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
        $contactInput = preg_replace('/\D/', '', $request->input('contact'));
        $request->merge(['contact' => $contactInput]);

        $loadData = $request->validate([
            'teacherName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:teachers,email',
            'contact' => [
                'required',
                'string',
                'min:10',
                'max:13',
                'regex:/^(09|\+639|639)\d{9}$/'
            ],
            [
                'teacherName.regex' => 'Teacher name should only contain letters, spaces, and periods.',
                'contact.regex' => 'Please enter a valid Philippine mobile number (09/+639/639 format).',
                'contact.min' => 'Mobile number must be at least 10 digits.',
                'contact.max' => 'Mobile number cannot exceed 13 digits.'
            ]
        ]);

        try {
            // Normalize the contact number before saving
            $loadData['contact'] = $this->normalizePhoneNumber($contactInput);

            // Create a new teacher record
            $teacher = Teachers::create($loadData);

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Teacher creation failed', [
                'error' => $e->getMessage(),
                'input' => $request->all()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to add teacher. Please try again.')
                ->withInput();
        }

        return redirect()->route('admin.teacher')
            ->with('success', 'Teacher added successfully!');
    }

    // Helper method to normalize phone number
    private function normalizePhoneNumber($number) {
        // Remove all non-digit characters
        $cleaned = preg_replace('/\D/', '', $number);

        // Standardize to +639 format
        if (strpos($cleaned, '09') === 0) {
            return '+63' . substr($cleaned, 1);
        } elseif (strpos($cleaned, '639') === 0) {
            return '+' . $cleaned;
        } elseif (strpos($cleaned, '9') === 0) {
            return '+639' . $cleaned;
        }

        return $cleaned;
    }

    // Function for editing teacher loads
    public function editLoad(Teachers $teachers) {
        return view('admin-modals.editTeacher', ['teachers' => $teachers]);
    }

    // Function for updating teacher loads
    public function updateLoad(Request $request, $id) {
        // Remove non-digit characters from contact input
        $contactInput = preg_replace('/\D/', '', $request->input('contact'));
        $request->merge(['contact' => $contactInput]);

        $request->validate([
            'teacherName' => 'required|string',
            'email' => 'required|string',
            'contact' => [
                'required',
                'string',
                'min:10',
                'max:13',
                'regex:/^(09|\+639|639)\d{9}$/'
            ]
        ], [
            'contact.regex' => 'Please enter a valid Philippine mobile number (09/+639/639 format).',
            'contact.min' => 'Mobile number must be at least 10 digits.',
            'contact.max' => 'Mobile number cannot exceed 13 digits.'
        ]);

        try {
            $teacher = Teachers::findOrFail($id);

            // Normalize the contact number before saving
            $normalizedContact = $this->normalizePhoneNumber($contactInput);

            $teacher->update([
                'teacherName' => $request->input('teacherName'),
                'email' => $request->input('email'),
                'contact' => $normalizedContact,
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
