<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    public function create($farmId)
    {
        $farm = Farm::with('owner')->findOrFail($farmId); // Ownerの情報も一緒に取得
        $owner = $farm->owner; // $farmに紐づく$ownerを取得
        return view('backend.stores.create', compact('farm', 'owner'));
    }

    public function store(Request $request, $farmId)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'product_info' => 'required|string',
            'product_link' => 'required|string',
            'product_image' => 'required|image|max:1024', //1MBまで
        ]);

        $manager = new ImageManager(new Driver());

        try {
            // トランザクション開始
            DB::beginTransaction();
    
            $url = null;
            if ($request->hasFile('product_image')) {
                // ファイルから画像を読み込み
                $image = $manager->read($request->file('product_image')->getPathName());
    
                // 画像をリサイズ (ここでは幅300pxに設定)
                $image->scale(width: 300);
    
                // 画像を一時的なファイルとして保存
                $tempPath = tempnam(sys_get_temp_dir(), 'productImage') . '.jpg';
                $image->toPng()->save($tempPath);
    
                // 保存した一時ファイルをS3にアップロード
                $fileName = 'product_images/' . uniqid() . '.jpg';
                Storage::disk('s3')->put($fileName, file_get_contents($tempPath), 'public');
                $url = Storage::disk('s3')->url($fileName);
    
                // 一時ファイルを削除
                @unlink($tempPath);
            }
    
            // Storeインスタンスの作成と保存
            $store = new Store;
            $store->farm_id = $farmId;
            $store->product_name = $request->input('product_name');
            $store->product_info = $request->input('product_info');
            $store->product_link = $request->input('product_link');
            $store->product_image = $url;
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
        $store = Store::with('farm.owner')->findOrFail($id);
        $owner = optional($store->farm)->owner;
    
        return view('backend.stores.edit', compact('store', 'owner'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string|max:255',
            'product_info' => 'required|string',
            'product_link' => 'required|string',
            'product_image' => 'nullable|image|max:1024', //1MBまで
        ]);
    
        $manager = new ImageManager(new Driver());

        try {
            DB::beginTransaction();
    
            $store = Store::findOrFail($id);
            $store->product_name = $validated['product_name'];
            $store->product_info = $validated['product_info'];
            $store->product_link = $validated['product_link'];

            if ($request->hasFile('product_image')) {
                // 既存の画像をS3から削除
                if ($store->product_image) {
                    $existingImagePath = parse_url($store->product_image, PHP_URL_PATH);
                    Storage::disk('s3')->delete($existingImagePath);
                }
    
                // 新しい画像を処理
                $image = $manager->read($request->file('product_image')->getPathName());
                $image->scale(width: 300);
                $tempPath = tempnam(sys_get_temp_dir(), 'productImage') . '.jpg';
                $image->toPng()->save($tempPath);
    
                // S3にアップロード
                $fileName = 'product_images/' . uniqid() . '.jpg';
                Storage::disk('s3')->put($fileName, file_get_contents($tempPath), 'public');
                $url = Storage::disk('s3')->url($fileName);
    
                // 一時ファイルを削除
                @unlink($tempPath);
    
                // データベースを更新
                $store->product_image = $url;
            }

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
        $owner = $store->farm->owner; // $farmに紐づく$ownerを取得
        // S3から画像を削除
        if ($store->product_image) {
            Storage::disk('s3')->delete(parse_url($store->product_image, PHP_URL_PATH));
        }
    
        // データベースから削除
        $store->delete();
    
        return redirect()->route('admin.backend.stores.show', ['id' => $owner->id])->with('success', '販売店が削除されました。');
    }
}
