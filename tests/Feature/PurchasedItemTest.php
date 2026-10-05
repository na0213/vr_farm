<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Farm;
use App\Models\PurchasedItem;
use App\Services\ImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * 購入した商品: 管理画面での登録・編集・削除と、牧場ページ・お取り寄せページでの表示。
 */
class PurchasedItemTest extends TestCase
{
    use RefreshDatabase;

    private Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        Storage::fake(ImageStorage::DISK);
        $this->admin = Admin::create(['name' => '管理者', 'email' => 'admin@example.com', 'password' => 'pass-word-1']);
    }

    private function makeFarm(string $name, bool $published = true): Farm
    {
        $farm = new Farm(['farm_name' => $name, 'catchcopy' => 'c', 'prefecture' => '北海道', 'address' => 'a', 'theme' => 't']);
        $farm->is_published = $published;
        $farm->save();

        return $farm;
    }

    public function test_admin_can_register_an_item_with_up_to_three_photos(): void
    {
        $farm = $this->makeFarm('鈴木牧場');

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.purchased-items.store', ['farm' => $farm->id]), [
                'item_name' => 'A2ミルク',
                'item_comment' => "すっきり。\nおいしい",
                'item_link' => 'https://example.com/milk',
                'item_image' => UploadedFile::fake()->image('package.jpg', 2400, 2400),
                'item_image_2' => UploadedFile::fake()->image('back.jpg'),
                'item_image_3' => UploadedFile::fake()->image('inside.png'),
            ])
            ->assertRedirect(route('admin.backend.purchased-items.create', ['farm' => $farm->id]));

        $item = PurchasedItem::sole();
        $this->assertSame('A2ミルク', $item->item_name);
        $this->assertSame($farm->id, $item->farm_id);
        $this->assertCount(3, array_unique($item->images()));
        foreach ($item->images() as $url) {
            $this->assertStringStartsWith('/uploads/purchased_item_images/', $url);
            Storage::disk(ImageStorage::DISK)->assertExists(ImageStorage::keyFromUrl($url));
        }

        $this->actingAs($this->admin, 'admins')
            ->get(route('admin.backend.purchased-items.create', ['farm' => $farm->id]))
            ->assertOk()
            ->assertSee('A2ミルク')
            ->assertSee('3枚');
    }

    public function test_second_and_third_photos_are_optional(): void
    {
        $farm = $this->makeFarm('鈴木牧場');

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.purchased-items.store', ['farm' => $farm->id]), [
                'item_name' => '牛乳',
                'item_image' => UploadedFile::fake()->image('package.jpg'),
            ])
            ->assertSessionHasNoErrors();

        $item = PurchasedItem::sole();
        $this->assertNull($item->item_image_2);
        $this->assertNull($item->item_image_3);
        $this->assertCount(1, $item->images());
    }

    public function test_photo_is_required_and_only_web_images_and_http_links_are_accepted(): void
    {
        $farm = $this->makeFarm('鈴木牧場');

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.purchased-items.store', ['farm' => $farm->id]), [
                'item_name' => '牛乳',
                'item_link' => 'javascript:alert(1)',
                'item_image' => UploadedFile::fake()->create('milk.heic', 100, 'image/heic'),
                'item_image_2' => UploadedFile::fake()->create('back.heic', 100, 'image/heic'),
            ])
            ->assertSessionHasErrors(['item_link', 'item_image', 'item_image_2']);

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.purchased-items.store', ['farm' => $farm->id]), ['item_name' => '牛乳'])
            ->assertSessionHasErrors(['item_image']);

        $this->assertSame(0, PurchasedItem::count());
    }

    public function test_admin_can_replace_and_remove_photos_and_delete_the_item(): void
    {
        $farm = $this->makeFarm('鈴木牧場');
        $stored = fn (string $name) => ImageStorage::storeResized(UploadedFile::fake()->image($name), 'purchased_item_images/'.$name);
        $item = PurchasedItem::create([
            'farm_id' => $farm->id,
            'item_name' => '牛乳',
            'item_link' => 'https://example.com/a',
            'item_image' => $stored('one.jpg'),
            'item_image_2' => $stored('two.jpg'),
            'item_image_3' => $stored('three.jpg'),
        ]);

        // 3枚目は差し替え、2枚目は消す。1枚目は「消す」を送っても消えない(必須なので)
        $this->actingAs($this->admin, 'admins')
            ->put(route('admin.backend.purchased-items.update', $item->id), [
                'item_name' => 'A2ミルク',
                'item_comment' => '',
                'item_link' => '',
                'item_image_3' => UploadedFile::fake()->image('new.jpg'),
                'remove_item_image' => '1',
                'remove_item_image_2' => '1',
            ])
            ->assertRedirect(route('admin.backend.purchased-items.create', ['farm' => $farm->id]));

        $item->refresh();
        $disk = Storage::disk(ImageStorage::DISK);
        $this->assertSame('A2ミルク', $item->item_name);
        $this->assertNull($item->item_link);
        $this->assertSame('/uploads/purchased_item_images/one.jpg', $item->item_image);
        $disk->assertExists('purchased_item_images/one.jpg');
        $this->assertNull($item->item_image_2);
        $disk->assertMissing('purchased_item_images/two.jpg');
        $this->assertNotSame('/uploads/purchased_item_images/three.jpg', $item->item_image_3);
        $disk->assertMissing('purchased_item_images/three.jpg');
        $this->assertCount(2, $item->images());

        $this->actingAs($this->admin, 'admins')
            ->delete(route('admin.backend.purchased-items.destroy', $item->id))
            ->assertRedirect(route('admin.backend.purchased-items.create', ['farm' => $farm->id]));

        $this->assertNull(PurchasedItem::find($item->id));
        foreach ($item->images() as $url) {
            $disk->assertMissing(ImageStorage::keyFromUrl($url));
        }
    }

    public function test_farm_page_shows_its_items_with_comment_and_link(): void
    {
        $farm = $this->makeFarm('鈴木牧場');
        $other = $this->makeFarm('ほかの牧場');
        PurchasedItem::create(['farm_id' => $farm->id, 'item_name' => 'A2ミルク', 'item_comment' => "すっきり。\n<b>おいしい</b>", 'item_link' => 'https://example.com/milk', 'item_image' => '/uploads/purchased_item_images/a.jpg']);
        PurchasedItem::create(['farm_id' => $other->id, 'item_name' => 'ほかの卵', 'item_image' => '/uploads/purchased_item_images/b.jpg']);

        $this->get(route('farm.show', $farm->id))
            ->assertOk()
            ->assertSee('買ってみた')
            ->assertSee('A2ミルク')
            ->assertSee('すっきり。<br />', false)
            ->assertSee('&lt;b&gt;おいしい&lt;/b&gt;', false)
            ->assertSee('href="https://example.com/milk"', false)
            ->assertSee('販売ページを見る')
            ->assertDontSee('ほかの卵')
            // 牧場情報(INFO)と訪問記(NOTE)の間に出す
            ->assertSeeInOrder(['牧場情報', '買ってみた', '訪問記・取材']);

        // 写真が1枚だけなら、切り替えの小さい写真は出さない
        $this->get(route('farm.show', $farm->id))->assertDontSee('枚目を見る');

        // 登録が無い牧場では、欄ごと出さない
        $this->get(route('farm.show', $this->makeFarm('空の牧場')->id))
            ->assertOk()
            ->assertDontSee('買ってみた');
    }

    public function test_cards_with_several_photos_show_thumbnails_to_switch(): void
    {
        $farm = $this->makeFarm('鈴木牧場');
        PurchasedItem::create([
            'farm_id' => $farm->id,
            'item_name' => 'A2ミルク',
            'item_image' => '/uploads/purchased_item_images/package.jpg',
            'item_image_3' => '/uploads/purchased_item_images/inside.jpg', // 2枚目が空でも詰めて出す
        ]);

        foreach ([route('farm.show', $farm->id), route('products.index')] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('src="/uploads/purchased_item_images/package.jpg"', false)
                ->assertSee('1枚目を見る')
                ->assertSee('2枚目を見る')
                ->assertDontSee('3枚目を見る')
                ->assertSee('src="/uploads/purchased_item_images/inside.jpg"', false);
        }
    }

    public function test_top_page_has_a_product_search_entry_that_links_to_the_products_page(): void
    {
        $farm = $this->makeFarm('鈴木牧場');
        PurchasedItem::create(['farm_id' => $farm->id, 'item_name' => 'A2ミルク', 'item_image' => '/uploads/purchased_item_images/a.jpg']);

        $this->get(route('index'))
            ->assertOk()
            ->assertSeeInOrder(['牧場検索', '商品検索'])
            ->assertSee('href="' . route('products.index') . '"', false)
            ->assertSee('src="/uploads/purchased_item_images/a.jpg"', false)
            // 商品を並べる欄はやめた(増えても、トップが長くならない)
            ->assertDontSee('A2ミルク');
    }

    public function test_top_page_hides_the_product_search_entry_when_nothing_is_registered(): void
    {
        $this->makeFarm('鈴木牧場');

        $this->get(route('index'))
            ->assertOk()
            ->assertDontSee('商品検索')
            ->assertDontSee('concept--products', false);
    }

    public function test_products_page_groups_items_by_farm(): void
    {
        $suzuki = $this->makeFarm('鈴木牧場');
        $sasaki = $this->makeFarm('SASAKI FARM');
        PurchasedItem::create(['farm_id' => $suzuki->id, 'item_name' => 'A2ミルク', 'item_image' => '/uploads/purchased_item_images/a.jpg']);
        PurchasedItem::create(['farm_id' => $sasaki->id, 'item_name' => '短角牛', 'item_image' => '/uploads/purchased_item_images/b.jpg']);

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSeeInOrder(['鈴木牧場', 'A2ミルク', 'SASAKI FARM', '短角牛'])
            // 販売ページが無い商品にはボタンを出さない
            ->assertDontSee('販売ページを見る');
    }

    public function test_products_page_shows_a_placeholder_when_nothing_is_registered(): void
    {
        $this->makeFarm('鈴木牧場');

        $this->get(route('products.index'))
            ->assertOk()
            ->assertSee('ただいま準備中');
    }
}
