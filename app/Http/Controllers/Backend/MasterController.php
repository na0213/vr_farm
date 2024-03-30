<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Farm;
use App\Models\Kind;
use App\Models\Keyword;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MasterController extends Controller
{
    public function index() {
        $owner = Owner::find(Auth::guard('owners')->id());
        $farm = $owner->farm;
        return view('backend.ownerdashboard', compact('owner', 'farm'));
    }

    public function show($id)
    {
        $farm = Farm::with(['owner', 'kinds', 'keywords'])->findOrFail($id);
        $owner = $farm->owner;
        $kinds = Kind::all();
        $keywords = Keyword::all();
    
        return view('backend.masters.show', compact('owner', 'farm', 'kinds', 'keywords'));
    }

    public function edit(string $id)
    {
        $farm = Farm::with(['kinds', 'keywords'])->findOrFail($id);
        $owner = $farm->owner;
        $images = $farm->images;
        $selected_kinds = $farm->kinds->pluck('id')->toArray();
        $selected_keywords = $farm->keywords->pluck('id')->toArray();
        $kinds = Kind::all();
        $keywords = Keyword::all();
    
        return view('backend.masters.edit', compact('owner', 'farm', 'images', 'kinds', 'keywords', 'selected_kinds', 'selected_keywords'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'catchcopy' => 'nullable|string|max:500',
            'prefecture' => 'required|string|max:255',
            'address' => 'nullable|string',
            'vr' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'kinds' => 'nullable|array',
            'kinds.*' => 'exists:kinds,id',
            'keywords' => 'nullable|array',
            'keywords.*' => 'exists:keywords,id',
            'farm_info' => 'nullable|string',
            'is_published' => 'required|boolean',
        ]);
    
        try {
            DB::beginTransaction();
    
            $farm = Farm::findOrFail($id);
            $farm->farm_name = $validated['name'];
            $farm->catchcopy = $validated['catchcopy'];
            $farm->prefecture = $validated['prefecture'];
            $farm->address = $validated['address'];
            $farm->vr = $validated['vr'];
            $farm->theme = $validated['theme'];
            $farm->farm_info = $request->input('editor1'); // CKEditor からの入力
            $farm->is_published = $validated['is_published'];
            $farm->save();
    
            // kinds と keywords の関連付けを更新
            $farm->kinds()->sync($validated['kinds'] ?? []);
            $farm->keywords()->sync($validated['keywords'] ?? []);
    
            DB::commit();
    
            return redirect()->route('owner.backend.masters.show', ['id' => $farm->owner_id])
                ->with('message', '牧場の情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function posts($id)
    {
        // 生産者（owner）が所有する農場を取得
        $farm = Farm::with(['ownerposts', 'posts'])->findOrFail($id);
    
        // 生産者が所有する農場に紐づく投稿を取得
        $ownerposts = $farm->ownerposts()->get();
        $posts = $farm->posts()->get();
    
        return view('backend.masters.posts', compact('ownerposts', 'posts', 'farm'));
    }
}
