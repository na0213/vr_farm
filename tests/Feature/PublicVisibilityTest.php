<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Farm;
use App\Models\PurchasedItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private function makeFarm(string $name, bool $published): Farm
    {
        $farm = new Farm([
            'farm_name' => $name,
            'catchcopy' => 'キャッチ',
            'prefecture' => '北海道',
            'address' => '住所',
            'theme' => 'テーマ',
        ]);
        $farm->is_published = $published;
        $farm->save();

        return $farm;
    }

    private function makeArticle(string $title, bool $published): Article
    {
        $article = new Article();
        $article->title = $title;
        $article->article_content = '<p>本文</p>';
        $article->article_images = '[]';
        $article->is_published = $published;
        $article->save();

        return $article;
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_published_farm_is_visible_and_unpublished_farm_is_not(): void
    {
        $open = $this->makeFarm('公開牧場', true);
        $closed = $this->makeFarm('非公開牧場', false);

        $this->get(route('farm.show', $open->id))->assertOk();
        $this->get(route('farm.show', $closed->id))->assertNotFound();
    }

    public function test_farm_search_lists_only_published_farms(): void
    {
        $this->makeFarm('公開牧場', true);
        $this->makeFarm('非公開牧場', false);

        $this->get(route('farm.index'))
            ->assertOk()
            ->assertSee('公開牧場')
            ->assertDontSee('非公開牧場');
    }

    public function test_sitemap_excludes_unpublished_farms(): void
    {
        $open = $this->makeFarm('公開牧場', true);
        $closed = $this->makeFarm('非公開牧場', false);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee($open->id)
            ->assertDontSee($closed->id);
    }

    public function test_products_of_unpublished_farms_are_not_listed(): void
    {
        $open = $this->makeFarm('公開牧場', true);
        $closed = $this->makeFarm('非公開牧場', false);

        foreach ([[$open, '公開の卵', 'open'], [$closed, '非公開の卵', 'closed']] as [$farm, $name, $image]) {
            PurchasedItem::create([
                'farm_id' => $farm->id,
                'item_name' => $name,
                'item_link' => 'https://example.com/' . $farm->id,
                'item_image' => "/uploads/purchased_item_images/{$image}.jpg",
            ]);
        }

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('公開の卵')
            ->assertDontSee('非公開の卵');

        // トップの商品検索の入口に使う写真も、公開中の牧場のものだけ
        // (非公開の牧場の商品のほうが新しくても、そちらは選ばれない)
        $this->get(route('index'))
            ->assertOk()
            ->assertSee('/uploads/purchased_item_images/open.jpg', false)
            ->assertDontSee('/uploads/purchased_item_images/closed.jpg', false);
    }

    public function test_draft_article_returns_404_but_published_article_is_visible(): void
    {
        $published = $this->makeArticle('公開記事', true);
        $draft = $this->makeArticle('下書き記事', false);

        $this->get(route('article.show', $published->id))->assertOk()->assertSee('公開記事');
        $this->get(route('article.show', $draft->id))->assertNotFound();
        $this->get(route('index'))->assertSee('公開記事')->assertDontSee('下書き記事');
    }

    public function test_old_animal_welfare_url_redirects_permanently(): void
    {
        $this->get('/animal-welfare')->assertStatus(301)->assertRedirect('/kodawari');
        $this->get('/kodawari')->assertOk();
    }
}
