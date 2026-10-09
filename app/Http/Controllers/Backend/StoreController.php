<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    // 登録済みの一覧(編集へのリンク)と、新規登録のフォーム
    public function create($farmId)
    {
        $farm = Farm::with('stores')->findOrFail($farmId);
        return view('backend.stores.create', compact('farm'));
    }

    public function store(Request $request, $farmId)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string',
            'store_link' => 'nullable|string',
        ]);


        try {
            // トランザクション開始
            DB::beginTransaction();

            $store = new Store;
            $store->farm_id = $farmId;
            $store->store_name = $request->input('store_name');
            $store->store_address = $request->input('store_address');
            $store->store_link = $request->input('store_link');
            $store->save();
    
            // トランザクションコミット
            DB::commit();
            return redirect()->route('admin.backend.stores.create', ['farm' => $farmId])->with('success', '登録されました。');
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }
    }

    public function edit($id)
    {
        $store = Store::findOrFail($id);
    
        return view('backend.stores.edit', compact('store'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'nullable|string',
            'store_link' => 'nullable|string',
        ]);
    

        try {
            DB::beginTransaction();
    
            $store = Store::findOrFail($id);
            $store->store_name = $validated['store_name'];
            $store->store_address = $validated['store_address'];
            $store->store_link = $validated['store_link'];

            $store->save();
    
            DB::commit();
    
            return redirect()->route('admin.backend.stores.create', ['farm' => $store->farm->id])
                ->with('message', '情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function destroy(string $id)
    {
        $store = Store::findOrFail($id);
        $farmId = $store->farm_id;

        // データベースから削除
        $store->delete();

        return redirect()->route('admin.backend.stores.create', ['farm' => $farmId])->with('success', '販売店が削除されました。');
    }
}
