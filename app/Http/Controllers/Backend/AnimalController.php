<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Animal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ImageStorage;

class AnimalController extends Controller
{
public function create($farmId)
    {
        $farm = Farm::findOrFail($farmId);

        // 追加: この牧場に紐付いている動物たちを取得する
        // （登録後に一覧で表示するため）
        $animals = Animal::where('farm_id', $farmId)->get();

        // animals をビューに渡す
        return view('backend.animals.create', compact('farm', 'animals'));
    }

    public function store(Request $request, $farmId)
    {
        $request->validate([
            'animal_name' => 'required|string|max:255',
            'animal_info' => 'required|string',
            'animal_image' => 'required|image|max:3072', // 3MBまで
        ]);

        try {
            // トランザクション開始
            DB::beginTransaction();

            $url = null;
            if ($request->hasFile('animal_image')) {
                // ファイルのパスを取得
                $image = $request->file('animal_image');

                // ファイル名を生成
                $fileName = 'animal_images/' . uniqid() . '.jpg';

                // リサイズして画像を保存
                $url = ImageStorage::storeResized($image, $fileName);
            }

            // Animalインスタンスの作成と保存
            $animal = new Animal;
            $animal->farm_id = $farmId;
            $animal->animal_name = $request->input('animal_name');
            $animal->animal_info = $request->input('animal_info');
            $animal->animal_image = $url;
            $animal->is_vr = $request->has('is_vr');
            $animal->save();

            // トランザクションコミット
            DB::commit();
            return redirect()->route('admin.backend.animals.create', ['farm' => $farmId])->with('success', '登録されました。');
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }
    }

    public function edit($id)
    {
        $animal = Animal::findOrFail($id);
    
        return view('backend.animals.edit', compact('animal'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'animal_name' => 'required|string|max:255',
            'animal_info' => 'required|string',
            'animal_image' => 'nullable|image|max:3072', // 3MBまで
        ]);

        try {
            DB::beginTransaction();

            $animal = Animal::findOrFail($id);
            $animal->animal_name = $validated['animal_name'];
            $animal->animal_info = $validated['animal_info'];
            $animal->is_vr = $request->has('is_vr');

            if ($request->hasFile('animal_image')) {
                // 既存の画像を削除
                ImageStorage::delete($animal->animal_image);

                // 新しい画像を処理
                $image = $request->file('animal_image');
                $fileName = 'animal_images/' . uniqid() . '.jpg';

                // リサイズして保存
                $url = ImageStorage::storeResized($image, $fileName);

                // データベースを更新
                $animal->animal_image = $url;
            }

            $animal->save();

            DB::commit();

            return redirect()->route('admin.backend.animals.create', ['farm' => $animal->farm->id])
                ->with('message', '情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function destroy(string $id)
    {
        $animal = Animal::findOrFail($id);
        $farmId = $animal->farm_id;
        // 画像を削除
        ImageStorage::delete($animal->animal_image);

        // データベースから削除
        $animal->delete();
    
        return redirect()->route('admin.backend.animals.create', ['farm' => $farmId])->with('success', '動物が削除されました。');
    }
}
