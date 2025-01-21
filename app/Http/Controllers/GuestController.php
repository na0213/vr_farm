<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Post;
use App\Models\Article;
use App\Models\Keyword;
use App\Models\Kind;

class GuestController extends Controller
{
    public function top()
    {
        $articles = Article::where('is_published', 1)
        ->select('id', 'title', 'article_images')
        ->paginate(8);
        return view('home', compact('articles'));
    }
    
    public function index(Request $request)
    {
        // 基本的なクエリ
        $query = Farm::query()->with(['kinds', 'keywords', 'farmImages']);
    
        // 「キーワード検索」: すべての内容から部分一致を検索
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('farm_name', 'like', '%' . $keyword . '%') // 牧場名
                    ->orWhere('catchcopy', 'like', '%' . $keyword . '%') // キャッチコピー
                    ->orWhere('prefecture', 'like', '%' . $keyword . '%') // 都道府県
                    ->orWhere('farm_info', 'like', '%' . $keyword . '%') // farm_info を検索対象に追加
                    ->orWhereHas('keywords', function ($q) use ($keyword) { // キーワード
                        $q->where('keyword', 'like', '%' . $keyword . '%');
                    })
                    ->orWhereHas('kinds', function ($q) use ($keyword) { // 種別
                        $q->where('kind', 'like', '%' . $keyword . '%');
                    });
            });
        }
    
        // 「都道府県検索」
        if ($request->filled('prefectures')) {
            $query->whereIn('prefecture', $request->prefectures);
        }
    
        // 「キーワード検索」: キーワードIDで検索
        if ($request->filled('keywords')) {
            $query->whereHas('keywords', function ($q) use ($request) {
                $q->whereIn('keywords.id', $request->keywords);
            });
        }
    
        // 「種別検索」
        if ($request->filled('kinds')) {
            $query->whereHas('kinds', function ($q) use ($request) {
                $q->whereIn('kinds.id', $request->kinds);
            });
        }
    
        // 結果を取得
        $farms = $query->get();
    
        // 検索フォームで利用する選択肢を取得
        $prefectures = Farm::distinct()->pluck('prefecture');
        $keywords = Keyword::all();
        $kinds = Kind::all();
    
        // ビューにデータを渡す
        return view('farm.map', compact('farms', 'prefectures', 'keywords', 'kinds'));
    }
    
    public function show($id)
    {
        // 関連するfarmとarticlesを取得
        $farm = Farm::with('animals')->findOrFail($id);
        $articles = Article::where('farm_id', $id)->where('is_published', true)->get(); // farm_idに一致する公開済みの記事を取得
        
        return view('farm.show', compact('farm', 'articles'));
    }

        public function about()
    {
        return view('about');
    }

    
    // コミュニティ
    public function communityIndex(Farm $farm)
    {
        $ownerposts = $farm->ownerposts()->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'is_owner' => true,
                'mypage' => null,
                'created_at' => $post->created_at,
                'image' => $post->owner_image ?? null,
            ];
        })->toArray();
    
        $posts = Post::with('mypage')->where('farm_id', $farm->id)->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'is_owner' => false,
                'mypage' => $post->mypage,
                'created_at' => $post->created_at,
                'image' => optional($post->mypage)->my_image ?? null,
            ];
        })->toArray();
    
        $allPosts = array_merge($ownerposts, $posts);
        usort($allPosts, function ($a, $b) {
            return $a['created_at'] <=> $b['created_at'];
        });
    
        $farmImages = $farm->farmImages()->orderBy('image_order')->get();

        return view('farm.community', compact('farm', 'allPosts', 'farmImages'));
    }

    public function showArticle($id)
    {
        // 記事のIDで記事を検索
        $article = Article::findOrFail($id);

        // 記事詳細ビューにデータを渡して表示
        return view('article.show', compact('article'));
    }
    
}
