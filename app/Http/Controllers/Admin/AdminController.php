<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Schedules;
use Illuminate\Http\Request;
use App\Models\Subjects;

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

    // Create subject function
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
        $request->validate([
            'subjectName' => 'required|string',
            'description' => 'nullable|string',
        ]);
        
        try {
            $subject->update([
                'subjectName' => $request->input('subjectName'),
                'description' => $request->input('description'),
            ]);

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
        return view('admin.teacher');
    }
    public function classroom() {
        return view('admin.classroom');
    }
    public function users() {
        return view('admin.users');
    }
}
