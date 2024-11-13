<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Teachers;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class NewRegistrationController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.temporary-register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store_user(Request $request)
    {
        // Validate the teacher exists
        $teacher = Teachers::findOrFail($request->input('teacher_id'));

        // Validate the request
        $validatedData = $request->validate([
            'teacher_id' => ['required', 'exists:teachers,id'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'user_role' => ['required', 'string', 'in:faculty,admin'],
        ], [
            // Custom error messages
            'teacher_id.exists' => 'The selected teacher does not exist.',
            'email.unique' => 'This email is already in use.',
            'user_role.in' => 'Invalid user role selected.',
        ]);

        try {
            // Checking if teacher already has a user
            $existingUser = User::where('teacher_id', $teacher->id)->first();
            if ($existingUser) {
                return response()->json([
                    'error' => 'A user account already exists for this teacher.'
                ], 422);
            }

            // Create the user
            $user = User::create([
                'name' => $teacher->teacherName,
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'user_role' => $validatedData['user_role'],
                'teacher_id' => $teacher->id,
            ]);

            event(new Registered($user));

            return response()->json([
                'success' => true,
                'message' => 'User created successfully for ' . $teacher->teacherName,
                'redirect' => route('admin.users')
            ], 200);

        } catch (\Exception $e) {
            // Logging exception errors
            Log::error('User creation error: ' . $e->getMessage());
            Log::error($e->getTraceAsString());

            // Return JSON error response
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
