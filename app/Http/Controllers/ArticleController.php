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

    // public function show($id)
    // {
    //     $owner = Farm::with('farm')->findOrFail($id);
    //     $farm = $owner->farm; // オーナーに紐づく牧場を取得
    //     return view('backend.owners.show', compact('owner', 'farm'));
    // }

    // public function edit($id)
    // {
    //     $owner = Farm::findOrFail($id);
    //     return view('backend.owners.edit', compact('owner'));
    // }
}
