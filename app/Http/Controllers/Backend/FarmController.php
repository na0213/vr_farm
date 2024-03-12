<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Farm;
use App\Models\Kind;
use App\Models\Keyword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FarmController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    public function create($ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        $kinds = Kind::all();
        $keywords = Keyword::all();
        return view('backend.farms.create', compact('owner', 'kinds', 'keywords'));
    }

    public function store(Request $request, $ownerId)
    {
        // バリデーション
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prefecture' => 'required|string|max:255',
            'address' => 'nullable|string',
            'vr' => 'nullable|string',
            'kinds' => 'nullable|array',
            'kinds.*' => 'exists:kinds,id',
            'keywords' => 'nullable|array',
            'keywords.*' => 'exists:keywords,id',
            'farm_info' => 'nullable|string',
            'is_published' => 'required|boolean',
        ]);
    
        try {
            // トランザクション開始
            DB::beginTransaction();
    
            $owner = Owner::findOrFail($ownerId);
            $farm = new Farm();
            $farm->owner_id = $owner->id;
            $farm->farm_name = $validated['name'];
            $farm->prefecture = $validated['prefecture'];
            $farm->address = $validated['address'];
            $farm->vr = $validated['vr'];
            $farm->farm_info = $request->input('editor1'); // CKEditor からの入力を直接取得
            $farm->is_published = $validated['is_published'];

            $farm->save();
    
            // kinds との関連付け
            if (!empty($validated['kinds'])) {
                $farm->kinds()->attach($validated['kinds']);
            }
    
            // keywords との関連付け
            if (!empty($validated['keywords'])) {
                $farm->keywords()->attach($validated['keywords']);
            }
    
            // トランザクションコミット
            DB::commit();
    
            return redirect()->route('admin.backend.farms.show', ['id' => $farm->id])
                ->with('message', '牧場が登録されました');
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }
    }

    public function show($id)
    {
        $farm = Farm::findOrFail($id);
        $kinds = Kind::all();
        $keywords = Keyword::all();
    
        return view('backend.farms.show', compact('farm', 'kinds', 'keywords'));
    }

    public function edit(string $id)
    {
        $owner = Owner::with('farm')->findOrFail($id);
        $farm = Farm::with(['kinds', 'keywords'])->findOrFail($id);
        $selected_kinds = $farm->kinds->pluck('id')->toArray();
        $selected_keywords = $farm->keywords->pluck('id')->toArray();
        $kinds = Kind::all();
        $keywords = Keyword::all();

        return view('backend.farms.edit', compact('owner', 'farm', 'kinds', 'keywords', 'selected_kinds', 'selected_keywords'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'prefecture' => 'required|string|max:255',
            'address' => 'nullable|string',
            'vr' => 'nullable|string',
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
            $farm->prefecture = $validated['prefecture'];
            $farm->address = $validated['address'];
            $farm->vr = $validated['vr'];
            $farm->farm_info = $request->input('editor1'); // CKEditor からの入力
            $farm->is_published = $validated['is_published'];
            $farm->save();
    
            // kinds と keywords の関連付けを更新
            $farm->kinds()->sync($validated['kinds'] ?? []);
            $farm->keywords()->sync($validated['keywords'] ?? []);
    
            DB::commit();
    
            return redirect()->route('admin.backend.farms.show', ['id' => $farm->id])
                ->with('message', '牧場の情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
