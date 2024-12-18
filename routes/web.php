<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Events\CalendarEventsController;
use App\Http\Controllers\Auth\NewRegistrationController;
use App\Http\Controllers\Admin\ConflictController;
use App\Http\Controllers\Admin\PrintController;
use App\Http\Controllers\Admin\EventController;

Route::get('/', function () {
    return view('auth.login');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Routes for users
Route::middleware(['auth', 'userMiddleware'])->group(function () {
    // Route to display the calendar of events in the user page
    Route::get('/event/home', [UserController::class, 'userIndex'])->name('event-calendar');

    // Route to display the schedule of the authenticated user
    Route::get('/faculty/schedule', [UserController::class, 'facultySchedule'])->name('schedule');

    // Route to display the notifications created by the admin in the calendar of events
    Route::get('/faculty/notifications-announcements', [UserController::class, 'notification'])->name('notification');
    Route::post('/faculty/notifications/marked-read/{id}', [UserController::class, 'is_read'])->name('read');

    // Route to display the conflicted schedule of the authenticated user and also prrint those conflicted schedules
    Route::get('/faculty/conflicted-schedules', [UserController::class, 'conflicted_schedule'])->name('conflicted_schedule');
    Route::get('/faculty/print-conflicted-schedules', [UserController::class, 'printConflictedSchedules'])->name('print_conflicted_schedules');
});

// Routes for admin
Route::middleware(['auth', 'adminMiddleware'])->group(function () {
    // Route for the home page
    Route::get('/admin/dashboard/home', [AdminController::class, 'adminIndex'])->name('admin.home');
    Route::post('/admin/dashboard/home-create', [AdminController::class, 'createEvent'])->name('admin.createEvent');
    Route::put('/admin/{id}/resize', [AdminController::class, 'resizeEvent']);
    Route::delete('/admin/{eventId}/delete-event', [AdminController::class, 'deleteEvent'])->name('admin.deleteEvent');
    Route::patch('/admin/{eventId}/drag-drop', [AdminController::class, 'dragEvent'])->name('admin.dragEvent');

    Route::get('/admin/print-schedule', [PrintController::class, 'print'])->name('print');
    Route::get('/admin/print-classroom', [PrintController::class, 'print_classroom'])->name('print_classroom');
    Route::get('/admin/print-calendar', [EventController::class, 'printCalendar'])->name('print-calendar');

    // Route for schedules page
    Route::get('/admin/dashboard/schedules', [ConflictController::class, 'schedules'])->name('admin.schedules');
    Route::post('/admin/dashboard/schedules/create-schedule', [ConflictController::class, 'createSchedule'])->name('admin.createSchedule');
    Route::get('/admin/dashboard/{schedules}/edit-schedule', [ConflictController::class, 'editSchedule'])->name('admin.editSchedule');
    Route::put('/admin/dashboard/{schedules}/update-schedule', [ConflictController::class, 'updateSchedule'])->name('admin.updateSchedule');
    Route::delete('/admin/dashboard/{schedulest}/delete-schedule', [ConflictController::class, 'deleteSchedule'])->name('admin.deleteSchedule');


    // Routes for the subject page
    Route::get('/admin/dashboard/subjects', [AdminController::class, 'subjects'])->name('admin.subjects');
    Route::post('/admin/dashboard/subjects/create', [AdminController::class, 'createSubject'])->name('admin.createSubject');
    Route::get('/admin/dashboard/{subject}/edit', [AdminController::class, 'editSubject'])->name('admin.editSubject');
    Route::put('/admin/dashboard/{subject}/update', [AdminController::class, 'updateSubject'])->name('admin.updateSubject');
    Route::delete('/admin/dashboard/{subject}/delete', [AdminController::class, 'deleteSubject'])->name('admin.deleteSubject');


    // Routes for the teachers page
    Route::get('/admin/dashboard/teachers', [AdminController::class, 'teacher'])->name('admin.teacher');
    Route::post('/admin/dashboard/teachers/create-load', [AdminController::class, 'createLoad'])->name('admin.createLoad');
    Route::get('/admin/dashboard/{load}/edit-load', [AdminController::class, 'editLoad'])->name('admin.editLoad');
    Route::put('/admin/dashboard/{load}/update-load', [AdminController::class, 'updateLoad'])->name('admin.updateLoad');
    Route::delete('/admin/dashboard/{load}/delete-load', [AdminController::class, 'deleteLoad'])->name('admin.deleteLoad');


    // Routes for the classroom page
    Route::get('/admin/dashboard/classroom', [AdminController::class, 'classroom'])->name('admin.classroom');
    Route::post('/admin/dashboard/classroom/create-room', [AdminController::class, 'createRoom'])->name('admin.createRoom');
    Route::get('/admin/dashboard/{classroom}/edit-room', [AdminController::class, 'editRoom'])->name('admin.editRoom');
    Route::put('/admin/dashboard/{classroom}/update-room', [AdminController::class, 'updateRoom'])->name('admin.updateRoom');
    Route::delete('/admin/dashboard/{classroom}/delete-room', [AdminController::class, 'deleteRoom'])->name('admin.deleteRoom');


    // Routes for the users page
    Route::get('/admin/dashboard/users', [AdminController::class, 'accounts'])->name('admin.users');
    Route::post('/admin/create_user', [NewRegistrationController::class, 'store_user'])->name('auth.store_user');
    Route::get('/admin/{users}/edit_user', [AdminController::class, 'edit_user'])->name('admin.edit_user');
    Route::patch('/admin/{user}/update-user', [AdminController::class, 'update_user'])->name('admin.update_user');
    Route::delete('/admin/{user}/delete-user', [AdminController::class, 'delete_user'])->name('admin.delete_user');
});


// API route for dynamic filtering of subjects based on the category selection
Route::group(['prefix' => 'api', 'as' => 'api.'], function () {
    Route::get('/subjects/by_category/{categoryId}', [AdminController::class, 'getCategory'])->name('subjects.by_category');
});



