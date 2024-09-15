<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function adminIndex() {
        return view('admin.home');
    }
    public function schedules() {
        return view('admin.schedules');
    }
    public function subjects() {
        return view('admin.subjects');
    }
    public function teachers() {
        return view('admin.teachers');
    }
    public function classroom() {
        return view('admin.classroom');
    }
    public function users() {
        return view('admin.users');
    }
}
