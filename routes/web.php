<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Events\CalendarEventsController;
use App\Http\Controllers\Auth\NewRegistrationController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// Routes for users
Route::middleware(['auth', 'userMiddleware'])->group(function () {
    Route::get('/dashboard/home', [UserController::class, 'userIndex'])->name('dashboard');
});

// Routes for admin
Route::middleware(['auth', 'adminMiddleware'])->group(function () {
    Route::get('/admin/dashboard/home', [AdminController::class, 'adminIndex'])->name('admin.home');
    Route::get('/admin/dashboard/schedules', [AdminController::class, 'schedules'])->name('admin.schedules');
    
    // Routes for the subject page
    Route::get('/admin/dashboard/subjects', [AdminController::class, 'subjects'])->name('admin.subjects');
    Route::post('/admin/dashboard/subjects/create', [AdminController::class, 'createSubject'])->name('admin.createSubject');
    Route::get('/admin/dashboard/{subject}/edit', [AdminController::class, 'editSubject'])->name('admin.editSubject');
    Route::put('/admin/dashboard/{subject}/update', [AdminController::class, 'updateSubject'])->name('admin.updateSubject');
    Route::delete('/admin/dashboard/{subject}/delete', [AdminController::class, 'deleteSubject'])->name('admin.deleteSubject');
    
    // Routes for the teachers page
    Route::get('/admin/dashboard/teachers', [AdminController::class, 'teacher'])->name('admin.teacher');
    Route::post('/admin/dashboard/classroom/create-load', [AdminController::class, 'createLoad'])->name('admin.createLoad');
    Route::get('/admin/dashboard/{classroom}/edit-load', [AdminController::class, 'editLoad'])->name('admin.editLoad');
    Route::put('/admin/dashboard/{classroom}/update-load', [AdminController::class, 'updateLoad'])->name('admin.updateLoad');
    Route::delete('/admin/dashboard/{classroom}/delete-load', [AdminController::class, 'deleteLoad'])->name('admin.deleteLoad');

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

// Routes for calendar of events
Route::middleware(['auth', 'adminMiddleware'])->group(function () {
    Route::get('/calendar/events', [CalendarEventsController::class, 'getCalendarEvents']);
    Route::delete('/calendar/event/{id}', [CalendarEventsController::class, 'deleteCalendarEvents']);
    Route::put('/calendar/event/{id}', [CalendarEventsController::class, 'updateCalendarEvents']);
    Route::put('/calendar/{id}/resize', [CalendarEventsController::class, 'resizeEvent']);
    Route::put('/calendar/search', [CalendarEventsController::class, 'searchEvent']);
});

