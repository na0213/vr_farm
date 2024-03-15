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

    public function edit($id)
    {
        $animal = Animal::with('farm.owner')->findOrFail($id);
        $owner = optional($animal->farm)->owner;
    
        return view('backend.animals.edit', compact('animal', 'owner'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'animal_name' => 'required|string|max:255',
            'animal_info' => 'required|string',
            'animal_image' => 'nullable|image|max:1024', //1MBまで
        ]);
    
        $manager = new ImageManager(new Driver());

        try {
            DB::beginTransaction();
    
            $animal = Animal::findOrFail($id);
            $animal->animal_name = $validated['animal_name'];
            $animal->animal_info = $validated['animal_info'];

            if ($request->hasFile('animal_image')) {
                // 既存の画像をS3から削除
                if ($animal->animal_image) {
                    $existingImagePath = parse_url($animal->animal_image, PHP_URL_PATH);
                    Storage::disk('s3')->delete($existingImagePath);
                }
    
                // 新しい画像を処理
                $image = $manager->read($request->file('animal_image')->getPathName());
                $image->scale(width: 300);
                $tempPath = tempnam(sys_get_temp_dir(), 'animalImage') . '.jpg';
                $image->toPng()->save($tempPath);
    
                // S3にアップロード
                $fileName = 'animal_images/' . uniqid() . '.jpg';
                Storage::disk('s3')->put($fileName, file_get_contents($tempPath), 'public');
                $url = Storage::disk('s3')->url($fileName);
    
                // 一時ファイルを削除
                @unlink($tempPath);
    
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
        $owner = $animal->farm->owner; // $farmに紐づく$ownerを取得
        // S3から画像を削除
        if ($animal->animal_image) {
            Storage::disk('s3')->delete(parse_url($animal->animal_image, PHP_URL_PATH));
        }
    
        // データベースから削除
        $animal->delete();
    
        return redirect()->route('admin.backend.owners.show', ['id' => $owner->id])->with('success', '動物が削除されました。');
    }
}
