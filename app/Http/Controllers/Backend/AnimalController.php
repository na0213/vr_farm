<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Animal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class AnimalController extends Controller
{
    public function create($farmId)
    {
        $farm = Farm::with('owner')->findOrFail($farmId); // Ownerの情報も一緒に取得
        $owner = $farm->owner; // $farmに紐づく$ownerを取得
        return view('backend.animals.create', compact('farm', 'owner'));
    }

    public function store(Request $request, $farmId)
    {
        $request->validate([
            'animal_name' => 'required|string|max:255',
            'animal_info' => 'required|string',
            'animal_image' => 'required|image|max:1024', //1MBまで
        ]);

        $manager = new ImageManager(new Driver());

        try {
            // トランザクション開始
            DB::beginTransaction();
    
            $url = null;
            if ($request->hasFile('animal_image')) {
                // ファイルから画像を読み込み
                $image = $manager->read($request->file('animal_image')->getPathName());
    
                // 画像をリサイズ (ここでは幅300pxに設定)
                $image->scale(width: 300);
    
                // 画像を一時的なファイルとして保存
                $tempPath = tempnam(sys_get_temp_dir(), 'animalImage') . '.jpg';
                $image->toPng()->save($tempPath);
    
                // 保存した一時ファイルをS3にアップロード
                $fileName = 'animal_images/' . uniqid() . '.jpg';
                Storage::disk('s3')->put($fileName, file_get_contents($tempPath), 'public');
                $url = Storage::disk('s3')->url($fileName);
    
                // 一時ファイルを削除
                @unlink($tempPath);
            }
    
            // Animalインスタンスの作成と保存
            $animal = new Animal;
            $animal->farm_id = $farmId;
            $animal->animal_name = $request->input('animal_name');
            $animal->animal_info = $request->input('animal_info');
            $animal->animal_image = $url;
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

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    
    public function edit($id)
    {
        $animal = Animal::findOrFail($id);
    
        // 必要に応じて他のデータも取得してビューに渡す
        return view('backend.animals.edit', compact('animal'));
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
