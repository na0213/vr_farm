<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Farm;
use App\Models\Product;
use App\Services\ImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * 撮影一覧(products): タイトルとコメントは任意。無い写真は、牧場ページで空の欄を出さない。
 */
class GalleryPhotoTest extends TestCase
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

    private function makeFarm(): Farm
    {
        $farm = new Farm(['farm_name' => '鈴木牧場', 'catchcopy' => 'c', 'prefecture' => '北海道', 'address' => 'a', 'theme' => 't']);
        $farm->is_published = true;
        $farm->save();

        return $farm;
    }

    public function test_photo_can_be_registered_without_title_and_comment(): void
    {
        $farm = $this->makeFarm();

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.products.store', ['farm' => $farm->id]), [
                'product_name' => '',
                'product_info' => '',
                'product_image' => UploadedFile::fake()->image('cow.jpg'),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.backend.products.create', ['farm' => $farm->id]));

        $product = Product::sole();
        $this->assertNull($product->product_name);
        $this->assertNull($product->product_info);
        Storage::disk(ImageStorage::DISK)->assertExists(ImageStorage::keyFromUrl($product->product_image));
    }

    public function test_title_and_comment_can_be_cleared(): void
    {
        $farm = $this->makeFarm();
        $product = Product::create(['farm_id' => $farm->id, 'product_name' => '冬も放牧', 'product_info' => '雪にもつよい', 'product_image' => null]);

        $this->actingAs($this->admin, 'admins')
            ->put(route('admin.backend.products.update', $product->id), [
                'product_name' => '',
                'product_info' => '',
                'product_link' => '',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.backend.products.create', ['farm' => $farm->id]));

        $this->assertNull($product->fresh()->product_name);
        $this->assertNull($product->fresh()->product_info);
    }

    public function test_farm_page_uses_the_farm_name_for_untitled_photos(): void
    {
        $farm = $this->makeFarm();
        Product::create(['farm_id' => $farm->id, 'product_name' => '冬も放牧', 'product_info' => null, 'product_image' => '/uploads/product_images/a.jpg']);
        Product::create(['farm_id' => $farm->id, 'product_name' => null, 'product_info' => null, 'product_image' => '/uploads/product_images/b.jpg']);

        $html = $this->get(route('farm.show', $farm->id))
            ->assertOk()
            ->assertSee('alt="冬も放牧"', false)
            ->assertSee('alt="鈴木牧場の写真"', false)
            ->getContent();

        // 写真に重ねるタイトルは、タイトルのある1枚だけ
        $this->assertSame(1, substr_count($html, 'drop-shadow-md'));
    }
}
