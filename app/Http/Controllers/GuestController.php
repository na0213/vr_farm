<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Article;
use App\Models\Keyword;
use App\Models\Kind;
use App\Models\Product;

class GuestController extends Controller
{
    public function top()
    {
        $articles = Article::where('is_published', 1)
        ->select('id', 'title', 'article_images')
        ->latest()
        ->paginate(8);

        // ECリンク付きの商品のみトップに表示(未整備なら非表示)
        $products = Product::listed()
            ->with('farm:id,farm_name,prefecture')
            ->latest()
            ->take(4)
            ->get();

        return view('home', compact('articles', 'products'));
    }

    // お取り寄せ(商品一覧)
    public function products()
    {
        $products = Product::listed()
            ->with('farm:id,farm_name,prefecture')
            ->latest()
            ->get();

        return view('products.index', compact('products'));
    }

    // 牧場のこだわりと、おいしい理由(入門ページ)
    public function kodawari()
    {
        return view('kodawari');
    }
    
    public function index()
    {
        // 公開中の牧場をすべて出し、絞り込みはブラウザ側で行う(静的サイトとして書き出すため)。
        // theme(牧場の紹介文)はキーワード検索の対象なので含める
        $farms = Farm::published()
            ->with(['kinds', 'keywords', 'farmImages'])
            ->select('id', 'farm_name', 'catchcopy', 'prefecture', 'theme')
            ->get();

        // 検索フォームで利用する選択肢を取得
        $prefectures = Farm::published()->distinct()->pluck('prefecture');
        $keywords = Keyword::all();
        $kinds = Kind::all();
    
        // ビューにデータを渡す
        return view('farm.map', compact('farms', 'prefectures', 'keywords', 'kinds'));
    }
    
    public function show($id)
    {
        // ビューで使う関連をまとめて取得(N+1回避)
        $farm = Farm::published()->with([
            'animals',
            'products',
            'stores',
            'kinds',
            'keywords',
            'farmImages' => fn ($q) => $q->orderBy('image_order'),
        ])->findOrFail($id);
        $articles = Article::where('farm_id', $id)->where('is_published', true)
            ->select('id', 'title', 'article_images', 'created_at')
            ->latest()
            ->get();

        return view('farm.show', compact('farm', 'articles'));
    }

        public function about()
    {
        return view('about');
    }

    public function showArticle($id)
    {
        // 公開中の記事だけ表示(下書きはURLが分かっても404)
        $article = Article::where('is_published', true)->findOrFail($id);

        // 記事詳細ビューにデータを渡して表示
        return view('article.show', compact('article'));
    }
    
}
