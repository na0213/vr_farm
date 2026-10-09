<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ImageStorage;

class ProductController extends Controller
{
    // 登録済みの一覧(編集へのリンク)と、新規登録のフォーム
    public function create($farmId)
    {
        $farm = Farm::with('products')->findOrFail($farmId);
        return view('backend.products.create', compact('farm'));
    }

    public function store(Request $request, $farmId)
    {
        // 撮影一覧は写真が主役なので、タイトルとコメントは任意
        $request->validate([
            'product_name' => 'nullable|string|max:255',
            'product_info' => 'nullable|string',
            'product_link' => 'nullable|string',
            'product_image' => 'required|image|max:3072', //1MBまで
        ]);

        try {
            // トランザクション開始
            DB::beginTransaction();
    
            $url = null;
            if ($request->hasFile('product_image')) {
                // ファイルのパスを取得
                $image = $request->file('product_image');

                // ファイル名を生成
                $fileName = 'product_images/' . uniqid() . '.jpg';

                // リサイズして画像を保存
                $url = ImageStorage::storeResized($image, $fileName, maxWidth: 1200);
            }
    
            // Storeインスタンスの作成と保存
            $product = new Product;
            $product->farm_id = $farmId;
            $product->product_name = $request->input('product_name');
            $product->product_info = $request->input('product_info');
            $product->product_link = $request->input('product_link');
            $product->product_image = $url;
            $product->save();
    
            // トランザクションコミット
            DB::commit();
            return redirect()->route('admin.backend.products.create', ['farm' => $farmId])->with('success', '登録されました。');
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
    
        return view('backend.products.edit', compact('product'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'product_name' => 'nullable|string|max:255',
            'product_info' => 'nullable|string',
            'product_link' => 'nullable|string',
            'product_image' => 'nullable|image|max:3072', //1MBまで
        ]);

        try {
            DB::beginTransaction();
    
            $product = Product::findOrFail($id);
            $product->product_name = $validated['product_name'] ?? null;
            $product->product_info = $validated['product_info'] ?? null;
            $product->product_link = $validated['product_link'];

            if ($request->hasFile('product_image')) {
                // 既存の画像を削除
                ImageStorage::delete($product->product_image);

                // 新しい画像をリサイズして保存
                $image = $request->file('product_image');
                $fileName = 'product_images/' . uniqid() . '.jpg';
                $url = ImageStorage::storeResized($image, $fileName, maxWidth: 1200);

                // データベースを更新
                $product->product_image = $url;
            }

            $product->save();
    
    
            DB::commit();
    
            return redirect()->route('admin.backend.products.create', ['farm' => $product->farm->id])
                ->with('message', '情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function destroy(string $id)
    {
        $product = Product::findOrFail($id);
        $farmId = $product->farm_id;
        // 画像を削除
        ImageStorage::delete($product->product_image);
    
        // データベースから削除
        $product->delete();
    
        return redirect()->route('admin.backend.products.create', ['farm' => $farmId])->with('success', '写真を削除しました。');
    }
}
