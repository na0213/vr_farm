<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Owner;

class OwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admin = Admin::find(Auth::guard('admins')->id());
        $owners = Owner::all();

        return view('backend.owners.index', compact('admin', 'owners'));
    }

    public function create()
    {
        return view('backend.owners.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:owners,email',
            'password' => 'required|string|min:8',
        ]);

        $owner = new Owner();
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->password = Hash::make($request->password);
        $owner->save();

        session()->flash('message', 'オーナーを登録しました。');

        return redirect()->route('admin.backend.owners.index');
    }

    public function show($id)
    {
        $owner = Owner::with('farm')->findOrFail($id);
        $farm = $owner->farm; // オーナーに紐づく牧場を取得
        return view('backend.owners.show', compact('owner', 'farm'));
    }

    public function edit($id)
    {
        $owner = Owner::findOrFail($id);
        return view('backend.owners.edit', compact('owner'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:owners,email,' . $id,
        ]);

        $owner = Owner::findOrFail($id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->save();

        return redirect()->route('admin.backend.owners.index')->with('message', 'オーナー情報を更新しました。');
    }

    public function destroy($id)
    {
        $owner = Owner::findOrFail($id);
        $owner->delete();
        return redirect()->route('admin.backend.owners.index')->with('message', 'オーナーを削除しました。');
    }
}
