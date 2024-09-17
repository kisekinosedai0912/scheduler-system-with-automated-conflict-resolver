<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SchedulesController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Events\CalendarEventsController;

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
    // Routes for the classroom page
    Route::get('/admin/dashboard/classroom', [AdminController::class, 'classroom'])->name('admin.classroom');
    // Routes for the classroom page
    Route::get('/admin/dashboard/users', [AdminController::class, 'users'])->name('admin.users');

});

// Routes for calendar of events
Route::middleware(['auth', 'adminMiddleware'])->group(function () {
    Route::get('/calendar/events', [CalendarEventsController::class, 'getCalendarEvents']);
    Route::delete('/calendar/event/{id}', [CalendarEventsController::class, 'deleteCalendarEvents']);
    Route::put('/calendar/event/{id}', [CalendarEventsController::class, 'updateCalendarEvents']);
    Route::put('/calendar/{id}/resize', [CalendarEventsController::class, 'resizeEvent']);
    Route::put('/calendar/search', [CalendarEventsController::class, 'searchEvent']);
});

