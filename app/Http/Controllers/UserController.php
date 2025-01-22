<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Post;
use App\Models\Article;
use App\Models\Keyword;
use App\Models\Kind;

class UserController extends Controller
{
    public function top()
    {
        $articles = Article::where('is_published', 1)
        ->select('id', 'title', 'article_images')
        ->paginate(8);
    
        return view('dashboard', compact('articles'));
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
        return view('user.farm.map', compact('farms', 'prefectures', 'keywords', 'kinds'));
    }
    
    public function show($id)
    {
        // 関連するfarmとarticlesを取得
        $farm = Farm::with('animals')->findOrFail($id);
        $articles = Article::where('farm_id', $id)->where('is_published', true)->get(); // farm_idに一致する公開済みの記事を取得
        return view('user.farm.show', compact('farm', 'articles'));
    }

    // お気に入り登録
    public function toggleFavorite(Request $request, Farm $farm)
    {
        $user = $request->user();
    
        $like = $farm->likes()->where('user_id', $user->id)->first();
    
        if ($like) {
            // すでにお気に入りなら削除する
            $like->delete();
            $isFavorite = false;
        } else {
            // お気に入りになければ追加する
            $farm->likes()->create(['user_id' => $user->id]);
            $isFavorite = true;
        }
    
        $favoritesCount = $farm->likes()->count();
    
        return response()->json([
            'isFavorite' => $isFavorite,
            'favoritesCount' => $favoritesCount
        ]);
    }

    // お気に入り一覧
    public function favorites(Request $request)
    {
        $user = $request->user();
        $favorites = $user->likes()->with('farm')->get()->map(function ($like) {
            return $like->farm;
        });
    
        // マイページが存在しない場合は空のコレクションを返す
        $followings = collect();
    
        if ($user->mypage) {
            // フォローしているマイページを取得
            $followings = $user->mypage->following()->with('followed')->get()->map(function ($follow) {
                return $follow->followed;
            });
        }
    
        return view('user.favorites', compact('favorites', 'followings'));
    }
    
    public function about()
    {
        return view('user.about');
    }
}
