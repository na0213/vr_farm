<?php

namespace App\Http\Controllers;

use App\Models\Farm;
use App\Models\Article;
use App\Models\Keyword;
use App\Models\Kind;
use App\Models\PurchasedItem;

class GuestController extends Controller
{
    public function top()
    {
        $articles = Article::where('is_published', 1)
        ->select('id', 'title', 'article_images')
        ->latest()
        ->paginate(8);

        // 購入した商品の新しいもの4件(未登録なら非表示)
        $products = PurchasedItem::listed()
            ->with('farm:id,farm_name,prefecture')
            ->latest('id')
            ->take(4)
            ->get();

        return view('home', compact('articles', 'products'));
    }

    // お取り寄せ(購入した商品を、牧場ごとに)
    public function products()
    {
        $farms = Farm::published()
            ->whereHas('purchasedItems')
            ->with('purchasedItems')
            ->select('id', 'farm_name', 'prefecture')
            ->orderBy('created_at')
            ->get();

        return view('products.index', compact('farms'));
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
            'purchasedItems',
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
