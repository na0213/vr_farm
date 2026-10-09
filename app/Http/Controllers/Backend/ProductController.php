<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\ImageStorage;
use App\Services\VideoClip;
use App\Exceptions\VideoClipException;

class ProductController extends Controller
{
    // 動画は長くても受け取り、最初の15秒だけ使う(開発サーバーの上限は .claude/php.d/uploads.ini)
    private const VIDEO_RULE = 'nullable|file|mimetypes:video/mp4,video/quicktime|max:204800'; //200MBまで

    private const MESSAGES = [
        'product_image.required_without' => '写真か動画を選んでください。',
        'product_video.mimetypes' => '動画は MP4 か MOV を選んでください。',
        'product_video.max' => '動画は200MBまでです。',
        'product_video.uploaded' => '動画を受け取れませんでした(大きすぎる可能性があります)。',
    ];

    // 登録済みの一覧(編集へのリンク)と、新規登録のフォーム
    public function create($farmId)
    {
        $farm = Farm::with('products')->findOrFail($farmId);
        return view('backend.products.create', compact('farm'));
    }

    public function store(Request $request, $farmId)
    {
        // 撮影一覧は写真が主役なので、タイトルとコメントは任意。写真か動画のどちらかは必要
        $request->validate([
            'product_name' => 'nullable|string|max:255',
            'product_info' => 'nullable|string',
            'product_link' => 'nullable|string',
            'product_image' => 'required_without:product_video|nullable|image|max:3072', //1MBまで
            'product_video' => self::VIDEO_RULE,
        ], self::MESSAGES);

        try {
            // トランザクション開始
            DB::beginTransaction();

            $url = null;
            $video = null;
            if ($request->hasFile('product_video')) {
                // 15秒・音なしの MP4 に変換し、最初の場面を一覧の写真にする
                $clip = VideoClip::store($request->file('product_video'), uniqid());
                $video = $clip['video'];
                $url = $clip['poster'];
            }

            if ($request->hasFile('product_image')) {
                // 写真も選んだときは、動画の最初の場面の代わりにその写真を使う
                ImageStorage::delete($url);

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
            $product->product_video = $video;
            $product->save();

            // トランザクションコミット
            DB::commit();
            return redirect()->route('admin.backend.products.create', ['farm' => $farmId])->with('success', '登録されました。');
        } catch (VideoClipException $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
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
            'product_video' => self::VIDEO_RULE,
            'remove_video' => 'nullable|boolean',
        ], self::MESSAGES);

        try {
            DB::beginTransaction();

            $product = Product::findOrFail($id);
            $product->product_name = $validated['product_name'] ?? null;
            $product->product_info = $validated['product_info'] ?? null;
            $product->product_link = $validated['product_link'];

            if ($request->hasFile('product_video')) {
                // 動画を差し替える。写真を選んでいなければ、一覧の写真も新しい動画の最初の場面にする
                $clip = VideoClip::store($request->file('product_video'), uniqid());
                ImageStorage::delete($product->product_video);
                $product->product_video = $clip['video'];

                if ($request->hasFile('product_image')) {
                    ImageStorage::delete($clip['poster']);
                } else {
                    ImageStorage::delete($product->product_image);
                    $product->product_image = $clip['poster'];
                }
            } elseif ($request->boolean('remove_video')) {
                // 動画を外す(一覧の写真はそのまま残し、写真の項目になる)
                ImageStorage::delete($product->product_video);
                $product->product_video = null;
            }

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
        } catch (VideoClipException $e) {
            DB::rollback();
            return back()->withInput()->withErrors(['error' => $e->getMessage()]);
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
        // 画像と動画を削除
        ImageStorage::delete($product->product_image);
        ImageStorage::delete($product->product_video);

        // データベースから削除
        $product->delete();

        return redirect()->route('admin.backend.products.create', ['farm' => $farmId])->with('success', '削除しました。');
    }
}
