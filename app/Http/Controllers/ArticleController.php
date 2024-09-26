<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\Farm;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $admin = Admin::find(Auth::guard('admins')->id());
        $articles = Article::with('farm')->get();  // 記事と関連する牧場情報を取得

        return view('backend.article.index', compact('admin', 'articles'));
    }

    public function create()
    {
        $farms = Farm::all();  // 牧場一覧を取得して記事作成時に選択できるように
        return view('backend.article.create', compact('farms'));
    }

    public function store(Request $request)
    {
        // dd($request);
        Log::info('CKEditor Content: ' . $request->input('editor1'));

        $request->validate([
            'farm_id' => 'required|string|max:255',
            'title' => 'required|string|max:500',
            // 'content' => 'required|string',
            'is_published' => 'required|boolean',
            'article_images.*' => 'image|max:3072', // 各画像が3MBまで
            // 'article_images' => 'required|image|max:3072', // 3MBまで
        ]);

        try {
            // トランザクション開始
            DB::beginTransaction();
            $uploadedImages = [];
            if ($request->hasFile('article_images')) {
                foreach ($request->file('article_images') as $image) {
                    if ($image) {
                        $fileName = 'article_images/' . uniqid() . '.' . $image->getClientOriginalExtension();
                        Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                        $uploadedImages[] = Storage::disk('s3')->url($fileName);
                    }
                }
            }

            $article = new Article;
            $article->farm_id = $request['farm_id'];
            $article->title = $request['title'];
            $article->article_content = $request->input('editor1'); // CKEditorの内容をここで保存
            $article->is_published = $request['is_published'];
            $article->article_images = json_encode($uploadedImages);
            // $article->article_images = $url; // 画像URLをJSONで保存
            $article->save();

            // トランザクションコミット
            DB::commit();
            return redirect()->route('admin.backend.article.index')->with('success', '登録されました。');
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }

    }

    public function show($id)
    {
        // 指定されたIDの記事を取得
        $article = Article::with('farm')->findOrFail($id);
    
        // 記事の詳細画面にデータを渡す
        return view('backend.article.show', compact('article'));
    }

    public function edit($id)
    {
        $article = Article::findOrFail($id);
        $farms = Farm::all();

        return view('backend.article.edit', compact('article', 'farms'));
    }

    public function update(Request $request, $id)
    {
        // バリデーション
        $request->validate([
            'farm_id' => 'required|string|max:255',
            'title' => 'required|string|max:500',
            'article_images.*' => 'image|max:3072', // 各画像が3MBまで
            'is_published' => 'required|boolean',
        ]);

        try {
            DB::beginTransaction();
            
            $article = Article::findOrFail($id);

            // 既存の画像を取得
            $existingImages = json_decode($article->article_images, true) ?: [];
            Log::info('Existing Images: ' . json_encode($existingImages));

            $uploadedImages = $existingImages; // 既存の画像を保持

            // 画像がアップロードされた場合の処理
            if ($request->hasFile('article_images')) {
                foreach ($request->file('article_images') as $index => $image) {
                    if ($image) {
                        // 既存の画像があれば削除
                        if (isset($existingImages[$index]) && !empty($existingImages[$index])) {
                            // S3 URLからキーを抽出して削除
                            $oldImagePath = parse_url($existingImages[$index], PHP_URL_PATH);
                            $oldImageKey = ltrim($oldImagePath, '/');

                            if (!empty($oldImageKey)) {
                                Storage::disk('s3')->delete($oldImageKey); // S3から古い画像を削除
                                Log::info('Deleted old image: ' . $oldImageKey);
                            }
                        }

                        // 新しい画像の保存
                        $fileName = 'article_images/' . uniqid() . '.' . $image->getClientOriginalExtension();
                        Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                        $uploadedImages[$index] = Storage::disk('s3')->url($fileName); // 新しい画像を配列の同じインデックスに格納
                    }
                }
                Log::info('Uploaded Images: ' . json_encode($uploadedImages)); // 新しい画像をログに出力
            }

            // 記事情報の更新
            $article->farm_id = $request['farm_id'];
            $article->title = $request['title'];
            $article->article_content = $request->input('editor1');
            $article->is_published = $request['is_published'];
            $article->article_images = json_encode($uploadedImages); // 更新された画像配列を保存
            $article->save();

            DB::commit();
            return redirect()->route('admin.backend.article.index')->with('success', '記事が更新されました。');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
    
            $article = Article::findOrFail($id);
    
            // S3に保存された画像を削除
            $existingImages = json_decode($article->article_images, true) ?: [];
            foreach ($existingImages as $imageUrl) {
                // S3 URLからキーを抽出して削除
                $oldImagePath = parse_url($imageUrl, PHP_URL_PATH);
                $oldImageKey = ltrim($oldImagePath, '/');
                if (!empty($oldImageKey)) {
                    Storage::disk('s3')->delete($oldImageKey); // S3から古い画像を削除
                    Log::info('Deleted old image: ' . $oldImageKey);
                }
            }
    
            // 記事自体の削除
            $article->delete();
    
            DB::commit();
            return redirect()->route('admin.backend.article.index')->with('success', '記事が削除されました。');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '削除に失敗しました。']);
        }
    }
    
}
