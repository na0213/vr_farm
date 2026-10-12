<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Farm;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Support\Str;

/**
 * 検索エンジン・AI 検索向けの構造化データ(JSON-LD)を組み立てる。
 * 画面に出ている内容だけを使い、空の値は出さない。
 */
class StructuredData
{
    // 運営者情報ページ(/about)に出している名前と合わせる
    private const OPERATOR = 'Natomi';

    public static function json(array $data): string
    {
        // </script> や & で <script> を壊されないよう、< > & はエスケープする
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_THROW_ON_ERROR);
    }

    /** 全ページ共通 */
    public static function website(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'FARM360',
            'url' => url('/'),
            'description' => '放牧や平飼いなど、こだわりを持って育てる牧場の取り組みと、そこで生まれるおいしいものをお届け！',
            'inLanguage' => 'ja',
            'publisher' => self::operator(),
        ];
    }

    public static function about(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'AboutPage',
            'url' => route('about.index'),
            'name' => '事業者情報',
            'inLanguage' => 'ja',
            // 説明は運営者情報ページの自己紹介と同じ内容にする
            'mainEntity' => self::operator() + [
                'description' => '個人の趣味として、牧場を訪ね、飼い方や育て方、エサや環境へのこだわりを伝えている。',
            ],
        ];
    }

    /** 牧場ページ。牧場そのものではなく「牧場について書いたページ」として表す */
    public static function farm(Farm $farm): array
    {
        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'url' => route('farm.show', $farm->id),
            'name' => "{$farm->farm_name}({$farm->prefecture})",
            'inLanguage' => 'ja',
            'dateModified' => $farm->updated_at?->toIso8601String(),
            'isPartOf' => ['@type' => 'WebSite', 'name' => 'FARM360', 'url' => url('/')],
            'breadcrumb' => self::breadcrumb('farm.show', $farm),
            'about' => [
                '@type' => 'LocalBusiness',
                '@id' => self::farmId($farm),
                'name' => $farm->farm_name,
                'description' => $farm->catchcopy,
                'url' => $farm->hp_link,
                'sameAs' => [$farm->instagram_link],
                'image' => $farm->farmImages->pluck('image_path')->map(self::absolute(...))->all(),
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressCountry' => 'JP',
                    'addressRegion' => $farm->prefecture,
                    'streetAddress' => $farm->address,
                ],
            ],
        ]);
    }

    public static function article(Article $article): array
    {
        $images = collect(json_decode($article->article_images, true) ?: [])
            ->filter()
            ->map(self::absolute(...))
            ->values()
            ->all();

        // 非公開の牧場は、リンク先が 404 になるので載せない
        $farm = $article->farm?->is_published ? $article->farm : null;

        return self::clean([
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => Str::limit(trim(strip_tags($article->article_content)), 110),
            'image' => $images,
            'inLanguage' => 'ja',
            'datePublished' => $article->created_at?->toIso8601String(),
            'dateModified' => $article->updated_at?->toIso8601String(),
            'author' => self::operator(),
            'publisher' => self::operator(),
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('article.show', $article->id)],
            'about' => $farm ? ['@type' => 'LocalBusiness', '@id' => self::farmId($farm), 'name' => $farm->farm_name] : null,
        ]);
    }

    // 個人の趣味として運営しているので Organization ではなく Person
    private static function operator(): array
    {
        return ['@type' => 'Person', 'name' => self::OPERATOR, 'url' => route('about.index')];
    }

    // 牧場ページ側と記事側で、同じ牧場を同じ ID で指す
    private static function farmId(Farm $farm): string
    {
        return route('farm.show', $farm->id).'#farm';
    }

    /** 画面のパンくずリストと同じ内容にする */
    private static function breadcrumb(string $name, mixed ...$params): array
    {
        $items = Breadcrumbs::generate($name, ...$params)
            ->values()
            ->map(fn ($crumb, $i) => [
                '@type' => 'ListItem',
                'position' => $i + 1,
                'name' => $crumb->title,
                'item' => $crumb->url,
            ])
            ->all();

        return ['@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    // アップロード画像は /uploads/... で保存しているので、絶対URLにする
    private static function absolute(string $path): string
    {
        return str_starts_with($path, '/') ? url($path) : $path;
    }

    /** null・空文字・空配列を取り除く(配列の並びは詰め直す) */
    private static function clean(array $data): array
    {
        $isList = array_is_list($data);
        $out = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::clean($value);
            }
            if ($value === null || $value === '' || $value === []) {
                continue;
            }
            $out[$key] = $value;
        }

        return $isList ? array_values($out) : $out;
    }
}
