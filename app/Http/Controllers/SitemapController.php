<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Article;
use App\Models\Farm;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create();

        // 固定ページを追加
        $sitemap->add(Url::create(route('index'))->setPriority(1.0)); // トップページ
        $sitemap->add(Url::create(route('kodawari.index'))->setPriority(0.8)); // こだわり
        $sitemap->add(Url::create(route('products.index'))->setPriority(0.8)); // お取り寄せ
        $sitemap->add(Url::create(route('about.index'))->setPriority(0.5)); // FARM360について
        $sitemap->add(Url::create(route('contact.form'))->setPriority(0.3)); // お問い合わせページ

        // 動的ページを追加
        $sitemap->add(Url::create(route('farm.index'))->setPriority(0.9)); // 牧場検索ページ
        $farms = Farm::published()->get(); // 公開中の牧場データを取得
        foreach ($farms as $farm) {
            $sitemap->add(
                Url::create(route('farm.show', $farm->id))
                    ->setPriority(0.7)
                    ->setLastModificationDate($farm->updated_at) // 更新日
            );
        }

        // 公開中の記事(下書きは含めない)
        foreach (Article::where('is_published', true)->get(['id', 'updated_at']) as $article) {
            $sitemap->add(
                Url::create(route('article.show', $article->id))
                    ->setPriority(0.7)
                    ->setLastModificationDate($article->updated_at)
            );
        }

        // サイトマップをXML形式で出力
        return Response::make($sitemap->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
