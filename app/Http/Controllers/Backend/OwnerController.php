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
        $owner->owner = $request->name;
        $owner->save();

        session()->flash('message', '種類が正常に登録されました。');

        return redirect()->route('admin.backend.owners.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
