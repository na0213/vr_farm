<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Farm;
use App\Models\FarmImage;
use App\Services\StructuredData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 検索エンジン・AI 検索向けの構造化データ(JSON-LD)。
 * 画面に出ている内容とずれないこと、空の値を出さないこと、スクリプトを壊せないことを確かめる。
 */
class StructuredDataTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function makeFarm(array $overrides = []): Farm
    {
        $farm = new Farm(array_merge([
            'farm_name' => '松浦牧場',
            'catchcopy' => '命が循環する牧場',
            'prefecture' => '宮崎県',
            'address' => '児湯郡新富町新田16597-2',
            'hp_link' => 'https://miyazaki.matsuuramilk.com/',
            'instagram_link' => 'https://www.instagram.com/matsuura/',
        ], $overrides));
        $farm->is_published = true;
        $farm->save();

        return $farm;
    }

    private function makeArticle(array $attrs = []): Article
    {
        $article = new Article;
        $article->title = $attrs['title'] ?? '平飼い卵・放牧卵、何が違う?';
        $article->article_content = $attrs['content'] ?? '<p>本文です。</p>';
        $article->article_images = $attrs['images'] ?? '[]';
        $article->farm_id = $attrs['farm_id'] ?? null;
        $article->is_published = true;
        $article->save();

        return $article;
    }

    /** HTML の中の JSON-LD をすべて配列にして返す */
    private function jsonLdBlocks(string $html): array
    {
        preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);

        return array_map(fn ($json) => json_decode($json, true, 512, JSON_THROW_ON_ERROR), $m[1]);
    }

    /** @type が一致する最初のブロック */
    private function blockOf(string $html, string $type): ?array
    {
        foreach ($this->jsonLdBlocks($html) as $block) {
            if (($block['@type'] ?? null) === $type) {
                return $block;
            }
        }

        return null;
    }

    public function test_website_block_is_on_every_public_page_and_names_the_operator(): void
    {
        foreach (['/', '/kodawari', '/products', '/farm/map', '/about'] as $path) {
            $site = $this->blockOf($this->get($path)->assertOk()->getContent(), 'WebSite');

            $this->assertNotNull($site, "$path に WebSite がない");
            $this->assertSame('https://schema.org', $site['@context']);
            $this->assertSame(url('/'), $site['url']);
            // 画面に出ている運営者名(運営者情報ページ)と同じ名前。個人の趣味なので Person
            $this->assertSame('Person', $site['publisher']['@type']);
            $this->assertSame('Natomi', $site['publisher']['name']);
            $this->assertSame(route('about.index'), $site['publisher']['url']);
        }
    }

    public function test_farm_page_describes_the_page_and_the_farm_it_is_about(): void
    {
        $farm = $this->makeFarm();
        FarmImage::create(['farm_id' => $farm->id, 'image_path' => '/uploads/farm/a.jpg', 'image_order' => 1]);

        $page = $this->blockOf($this->get(route('farm.show', $farm->id))->assertOk()->getContent(), 'WebPage');

        $this->assertNotNull($page);
        $this->assertSame(route('farm.show', $farm->id), $page['url']);
        $this->assertSame('ja', $page['inLanguage']);
        $this->assertSame($farm->updated_at->toIso8601String(), $page['dateModified']);

        // 牧場そのものではなく「牧場について書いたページ」として表す(about)
        $about = $page['about'];
        $this->assertSame('LocalBusiness', $about['@type']);
        $this->assertSame(route('farm.show', $farm->id).'#farm', $about['@id']);
        $this->assertSame('松浦牧場', $about['name']);
        $this->assertSame('命が循環する牧場', $about['description']);
        $this->assertSame('https://miyazaki.matsuuramilk.com/', $about['url']);
        $this->assertSame(['https://www.instagram.com/matsuura/'], $about['sameAs']);
        $this->assertSame('PostalAddress', $about['address']['@type']);
        $this->assertSame('JP', $about['address']['addressCountry']);
        $this->assertSame('宮崎県', $about['address']['addressRegion']);
        $this->assertSame('児湯郡新富町新田16597-2', $about['address']['streetAddress']);
        // 相対パスの画像は絶対URLにする
        $this->assertSame([url('/uploads/farm/a.jpg')], $about['image']);
    }

    public function test_farm_page_has_breadcrumbs_that_match_the_visible_ones(): void
    {
        $farm = $this->makeFarm();

        $page = $this->blockOf($this->get(route('farm.show', $farm->id))->getContent(), 'WebPage');
        $crumbs = $page['breadcrumb'];

        $this->assertSame('BreadcrumbList', $crumbs['@type']);
        $this->assertSame(
            [['Home', route('index')], ['牧場検索', route('farm.index')], ['松浦牧場', route('farm.show', $farm->id)]],
            array_map(fn ($i) => [$i['name'], $i['item']], $crumbs['itemListElement'])
        );
        $this->assertSame([1, 2, 3], array_column($crumbs['itemListElement'], 'position'));
    }

    public function test_farm_block_leaves_out_empty_fields_instead_of_printing_nulls(): void
    {
        $farm = $this->makeFarm(['catchcopy' => null, 'address' => null, 'hp_link' => null, 'instagram_link' => null]);

        $about = $this->blockOf($this->get(route('farm.show', $farm->id))->getContent(), 'WebPage')['about'];

        foreach (['description', 'url', 'sameAs', 'image'] as $key) {
            $this->assertArrayNotHasKey($key, $about, "$key が空のまま出ている");
        }
        $this->assertArrayNotHasKey('streetAddress', $about['address']);
        $this->assertSame('宮崎県', $about['address']['addressRegion']);
    }

    public function test_article_block_has_dates_author_and_the_farm_it_is_about(): void
    {
        $farm = $this->makeFarm();
        $article = $this->makeArticle([
            'title' => '松浦牧場を訪ねて',
            'content' => '<p>搾りたての牛乳を飲みました。</p>',
            'images' => json_encode(['/uploads/article/1.jpg', 'https://example.com/2.jpg']),
            'farm_id' => $farm->id,
        ]);

        $block = $this->blockOf($this->get(route('article.show', $article->id))->assertOk()->getContent(), 'Article');

        $this->assertNotNull($block);
        $this->assertSame('松浦牧場を訪ねて', $block['headline']);
        $this->assertSame('搾りたての牛乳を飲みました。', $block['description']);
        $this->assertSame([url('/uploads/article/1.jpg'), 'https://example.com/2.jpg'], $block['image']);
        $this->assertSame($article->created_at->toIso8601String(), $block['datePublished']);
        $this->assertSame($article->updated_at->toIso8601String(), $block['dateModified']);
        $this->assertSame(['Person', 'Natomi'], [$block['author']['@type'], $block['author']['name']]);
        $this->assertSame(['Person', 'Natomi'], [$block['publisher']['@type'], $block['publisher']['name']]);
        $this->assertSame(route('article.show', $article->id), $block['mainEntityOfPage']['@id']);
        $this->assertSame('松浦牧場', $block['about']['name']);
        // 牧場ページの LocalBusiness と同じ @id で、同じ牧場だと分かるようにする
        $this->assertSame(route('farm.show', $farm->id).'#farm', $block['about']['@id']);
    }

    public function test_column_article_without_a_farm_has_no_about(): void
    {
        $article = $this->makeArticle();

        $block = $this->blockOf($this->get(route('article.show', $article->id))->getContent(), 'Article');

        $this->assertArrayNotHasKey('about', $block);
        $this->assertArrayNotHasKey('image', $block);
    }

    public function test_article_does_not_point_to_an_unpublished_farm(): void
    {
        $farm = $this->makeFarm();
        $farm->is_published = false;
        $farm->save();
        $article = $this->makeArticle(['farm_id' => $farm->id]);

        $block = $this->blockOf($this->get(route('article.show', $article->id))->getContent(), 'Article');

        $this->assertArrayNotHasKey('about', $block);
    }

    public function test_about_page_describes_the_operator(): void
    {
        $page = $this->blockOf($this->get(route('about.index'))->assertOk()->getContent(), 'AboutPage');

        $this->assertNotNull($page);
        $this->assertSame(route('about.index'), $page['url']);
        $this->assertSame('Person', $page['mainEntity']['@type']);
        $this->assertSame('Natomi', $page['mainEntity']['name']);
        // 画面の自己紹介と同じ内容(個人の趣味)
        $this->assertStringContainsString('個人の趣味', $page['mainEntity']['description']);
    }

    public function test_script_closing_tags_in_data_cannot_break_out_of_the_block(): void
    {
        $farm = $this->makeFarm(['farm_name' => '悪い牧場</script><script>alert(1)</script>']);

        $html = $this->get(route('farm.show', $farm->id))->getContent();

        $this->assertStringNotContainsString('</script><script>alert(1)', $html);
        // 壊れずに読み戻せて、元の文字も残っている
        $this->assertSame('悪い牧場</script><script>alert(1)</script>', $this->blockOf($html, 'WebPage')['about']['name']);
    }

    public function test_json_encodes_japanese_and_slashes_readably(): void
    {
        $json = StructuredData::json(['name' => '牧場', 'url' => 'https://www.farm360.jp/']);

        $this->assertStringContainsString('"name":"牧場"', $json);
        $this->assertStringContainsString('https://www.farm360.jp/', $json);
    }

    public function test_robots_txt_points_crawlers_to_the_sitemap(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString("User-agent: *\nDisallow:\n", $robots);
        $this->assertStringContainsString('Sitemap: https://www.farm360.jp/sitemap.xml', $robots);
    }
}
