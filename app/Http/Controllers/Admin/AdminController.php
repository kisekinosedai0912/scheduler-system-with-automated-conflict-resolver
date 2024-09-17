<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Classroom;
use App\Models\Schedules;
use Illuminate\Http\Request;
use App\Models\Subjects;
use App\Models\Teachers;

class AdminController extends Controller
{
    // Function for returning view in the home page of the admin dashboard
    public function adminIndex() {
        return view('admin.home');
    }

    // Function for returning view in the schedule page of the admin dashboard
    public function schedules() {
        return view('admin.schedules');
    }

    // Function for returning view in the schedule page of the admin dashboard
    public function subjects() {
        $subjects = Subjects::all();

        return view('admin.subjects', ['subjects' => $subjects]);
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
            return redirect()->route('admin.subjects')->with('error', 'Record not found.');

        return redirect()->route('admin.subjects')->with('success', 'Record deleted successfully.');
    }

    // Function for returning view in the teacher loads page of the admin dashboard
    public function teacher() {
        $teachers = Teachers::all();
        $subjects = Subjects::all(); 

        return view('admin.teacher', [
            'teachers' => $teachers,
            'subjects' => $subjects
        ]);
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

            return redirect()->route('admin.teacher')->with('success', 'Record deleted successfully.');
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('admin.teacher')->with('error', 'Error: ' . $e->getMessage());
            
        } catch (\Exception $e) {
            return redirect()->route('admin.teacher')->with('error', 'Error occurred while deleting the record: ' . $e->getMessage());
        }
    }


    // Function for returning view in the classroom page
    public function classroom() {
        $rooms = Classroom::all();
        return view('admin.classroom', ['rooms' => $rooms]);
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
    






    public function users() {
        return view('admin.users');
    }
}
