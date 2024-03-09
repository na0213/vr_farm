<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use App\Models\Keyword;

class KeywordController extends Controller
{
    public function index()
    {
        $admin = Admin::find(Auth::guard('admins')->id());
        $keywords = Keyword::all();

        return view('backend.keywords.index', compact('admin', 'keywords'));
    }

    public function create()
    {
        return view('backend.keywords.create');
    }

    public function store(Request $request)
    {
        $keyword = new Keyword();
        $keyword->keyword = $request->name;
        $keyword->save();

        session()->flash('message', '種類が正常に登録されました。');

        return redirect()->route('admin.backend.keywords.index');
    }

    public function edit($id)
    {
        $keyword = Keyword::findOrFail($id);
        return view('backend.keywords.edit', compact('keyword'));
    }

    public function update(Request $request, $id)
    {
        $keyword = Keyword::findOrFail($id);
        $keyword->keyword = $request->name;
        $keyword->save();

        return redirect()->route('admin.backend.keywords.index')->with('message', '種類が更新されました');
    }

    public function destroy($id)
    {
        $keyword = Keyword::findOrFail($id);
        $keyword->delete();
        return redirect()->route('admin.backend.keywords.index')->with('message', '種類が削除されました');
    }
}
