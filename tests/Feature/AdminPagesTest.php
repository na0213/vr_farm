<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Animal;
use App\Models\Article;
use App\Models\Farm;
use App\Models\Keyword;
use App\Models\Kind;
use App\Models\Owner;
use App\Models\Product;
use App\Models\PurchasedItem;
use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 管理画面の主要な画面が、エラーなく開けること(不要コードの削除などで壊れていないか)を確認する。
 */
class AdminPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->actingAs(Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'pass-word-1']), 'admins');
    }

    public function test_admin_pages_render(): void
    {
        $owner = Owner::create(['name' => 'owner', 'email' => 'owner@example.com', 'password' => 'secret-pass']);
        $farm = new Farm(['owner_id' => $owner->id, 'farm_name' => 'テスト牧場', 'catchcopy' => 'c', 'prefecture' => '北海道', 'address' => 'a', 'theme' => 't']);
        $farm->is_published = true;
        $farm->save();
        $kind = Kind::create(['kind' => '牛']);
        $keyword = Keyword::create(['keyword' => '放牧']);
        $animal = Animal::create(['farm_id' => $farm->id, 'animal_name' => '花子', 'animal_info' => '説明', 'animal_image' => null, 'is_vr' => false]);
        $product = Product::create(['farm_id' => $farm->id, 'product_name' => '卵', 'product_info' => '説明', 'product_link' => 'https://example.com', 'product_image' => null]);
        $store = Store::create(['farm_id' => $farm->id, 'store_name' => '販売店', 'store_address' => '住所', 'store_link' => 'https://example.com']);
        $item = PurchasedItem::create(['farm_id' => $farm->id, 'item_name' => '牛乳', 'item_image' => '/uploads/purchased_item_images/a.jpg']);
        $article = new Article();
        $article->title = '記事';
        $article->article_content = '<p>本文</p>';
        $article->article_images = '[]';
        $article->is_published = true;
        $article->save();

        $pages = [
            'admin.dashboard' => [],
            'admin.backend.system' => [],
            'admin.password.edit' => [],
            'admin.backend.owners.index' => [],
            'admin.backend.owners.create' => [],
            'admin.backend.owners.show' => ['id' => $owner->id],
            'admin.backend.owners.edit' => ['id' => $owner->id],
            'admin.backend.farms.create' => ['owner' => $owner->id],
            'admin.backend.farms.edit' => ['id' => $farm->id],
            'admin.backend.farms.index' => [],
            'admin.admin.backend.farms.editImages' => ['farmId' => $farm->id],
            'admin.backend.animals.create' => ['farm' => $farm->id],
            'admin.backend.animals.edit' => ['id' => $animal->id],
            'admin.backend.products.create' => ['farm' => $farm->id],
            'admin.backend.products.edit' => ['id' => $product->id],
            'admin.backend.purchased-items.create' => ['farm' => $farm->id],
            'admin.backend.purchased-items.edit' => ['id' => $item->id],
            'admin.backend.stores.create' => ['farm' => $farm->id],
            'admin.backend.stores.edit' => ['id' => $store->id],
            'admin.backend.kinds.index' => [],
            'admin.backend.kinds.create' => [],
            'admin.backend.kinds.edit' => ['id' => $kind->id],
            'admin.backend.keywords.index' => [],
            'admin.backend.keywords.create' => [],
            'admin.backend.keywords.edit' => ['id' => $keyword->id],
            'admin.backend.article.index' => [],
            'admin.backend.article.create' => [],
            'admin.backend.article.show' => ['id' => $article->id],
            'admin.backend.article.edit' => ['id' => $article->id],
        ];

        foreach ($pages as $name => $params) {
            $this->get(route($name, $params))->assertOk();
        }
    }

    public function test_admin_root_redirects_to_the_dashboard(): void
    {
        $this->get('/admin')->assertRedirect('/admin/dashboard');
    }

    public function test_removed_admin_features_are_gone(): void
    {
        foreach (['verify-email', 'confirm-password'] as $path) {
            $this->get('/admin/' . $path)->assertNotFound();
        }
    }
}
