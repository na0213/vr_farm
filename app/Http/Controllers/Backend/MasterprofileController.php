<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MasterprofileController extends Controller
{
    public function edit()
    {
        $master = auth()->guard('owners')->user();
        return view('backend.masters.profile.edit', compact('master'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:masters,email,' . auth()->guard('owners')->id(),
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $master = auth()->guard('owners')->user();
        $master->name = $request->name;
        $master->email = $request->email;
        if ($request->password) {
            $master->password = Hash::make($request->password);
        }
        $master->save();

        return redirect()->route('owner.ownerdashboard')->with('status', '更新が成功しました.');
    }
}
