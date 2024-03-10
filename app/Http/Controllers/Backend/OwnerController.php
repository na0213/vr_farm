<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
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
        $owner = new Owner();
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->password = $request->password;
        $owner->save();

        session()->flash('message', '種類が正常に登録されました。');

        return redirect()->route('admin.backend.owners.index');
    }

    public function show($id)
    {
        $owner = Owner::findOrFail($id);
        return view('backend.owners.show', compact('owner'));
    }

    public function edit($id)
    {
        $owner = Owner::findOrFail($id);
        return view('backend.owners.edit', compact('owner'));
    }

    public function update(Request $request, $id)
    {
        $owner = Owner::findOrFail($id);
        $owner->name = $request->name;
        $owner->email = $request->email;
        $owner->save();

        return redirect()->route('admin.backend.owners.index')->with('message', '種類が更新されました');
    }

    public function destroy($id)
    {
        $owner = Owner::findOrFail($id);
        $owner->delete();
        return redirect()->route('admin.backend.owners.index')->with('message', '種類が削除されました');
    }
}
