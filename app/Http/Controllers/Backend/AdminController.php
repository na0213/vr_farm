<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index() {
        $admin = Admin::find(Auth::guard('admins')->id());

        return view('backend.dashboard', compact('admin'));
    }
}