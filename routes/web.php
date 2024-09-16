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
    Route::get('/admin/dashboard/subjects', [AdminController::class, 'subjects'])->name('admin.subjects');
    Route::get('/admin/dashboard/teachers', [AdminController::class, 'teachers'])->name('admin.teachers');
    Route::get('/admin/dashboard/classroom', [AdminController::class, 'classroom'])->name('admin.classroom');
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

