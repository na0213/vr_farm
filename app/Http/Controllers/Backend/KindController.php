<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Kind;

class KindController extends Controller
{
    public function index() {
        $admin = Admin::find(Auth::guard('admins')->id());
        $kinds = Kind::all();

        return view('backend.kinds.index', compact('admin', 'kinds'));
    }

    public function create()
    {
        return view('backend.kinds.create');
    }

    public function store(Request $request)
    {
        $kind = new Kind();
        $kind->kind = $request->name;
        $kind->save();

        session()->flash('message', '種類が正常に登録されました。');

        return redirect()->route('admin.backend.kinds.index');
    }

    public function edit($id)
    {
        $kind = Kind::findOrFail($id);
        return view('backend.kinds.edit', compact('kind'));
    }

    public function update(Request $request, $id)
    {
        $kind = Kind::findOrFail($id);
        $kind->kind = $request->name;
        $kind->save();

        return redirect()->route('admin.backend.kinds.index')->with('message', '種類が更新されました');
    }

    public function destroy($id)
    {
        $kind = Kind::findOrFail($id);
        $kind->delete();
        return redirect()->route('admin.backend.kinds.index')->with('message', '種類が削除されました');
    }
}
