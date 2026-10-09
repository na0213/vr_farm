<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Farm;
use App\Models\Product;
use App\Services\ImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\ExecutableFinder;
use Tests\TestCase;

/**
 * 撮影一覧の短い動画: 15秒・音なしの MP4 に変換し、最初の場面を一覧の写真にする。押したときだけ再生する。
 * 変換は本物の ffmpeg で確かめる(入っていない環境では飛ばす)。
 */
class GalleryVideoTest extends TestCase
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

    private function ffmpeg(string $name = 'ffmpeg'): string
    {
        $path = (new ExecutableFinder)->find($name, null, ['/opt/homebrew/bin', '/usr/local/bin']);
        if ($path === null) {
            $this->markTestSkipped("{$name} が無いので動画の変換は確かめない");
        }

        return $path;
    }

    // 20秒・音あり・横1920px のテスト動画(iPhone の動画の代わり)
    private function makeClip(): UploadedFile
    {
        $path = tempnam(sys_get_temp_dir(), 'clip').'.mov';
        Process::run([
            $this->ffmpeg(), '-y', '-loglevel', 'error',
            '-f', 'lavfi', '-i', 'testsrc2=duration=20:size=1920x1080:rate=30',
            '-f', 'lavfi', '-i', 'sine=frequency=440:duration=20',
            '-c:v', 'libx264', '-preset', 'ultrafast', '-c:a', 'aac', '-shortest', $path,
        ])->throw();

        return new UploadedFile($path, 'IMG_0001.MOV', 'video/quicktime', null, true);
    }

    public function test_video_is_trimmed_to_15_seconds_without_sound_and_gets_a_poster(): void
    {
        $farm = $this->makeFarm();

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.products.store', ['farm' => $farm->id]), [
                'product_video' => $this->makeClip(),
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('admin.backend.products.create', ['farm' => $farm->id]));

        $product = Product::sole();
        $this->assertStringStartsWith('/uploads/product_videos/', $product->product_video);
        $this->assertStringStartsWith('/uploads/product_images/', $product->product_image);
        $disk = Storage::disk(ImageStorage::DISK);
        $disk->assertExists(ImageStorage::keyFromUrl($product->product_image));

        $probe = Process::run([
            $this->ffmpeg('ffprobe'), '-v', 'error', '-show_entries', 'stream=codec_type,width,height:format=duration',
            '-of', 'json', $disk->path(ImageStorage::keyFromUrl($product->product_video)),
        ])->throw()->output();
        $info = json_decode($probe, true);

        $this->assertLessThanOrEqual(15.1, (float) $info['format']['duration']);
        $this->assertSame(['video'], array_column($info['streams'], 'codec_type'));
        $this->assertSame(1280, $info['streams'][0]['width']);
        $this->assertSame(720, $info['streams'][0]['height']);
    }

    public function test_photo_or_video_is_required(): void
    {
        $farm = $this->makeFarm();

        $this->actingAs($this->admin, 'admins')
            ->post(route('admin.backend.products.store', ['farm' => $farm->id]), ['product_name' => '牛'])
            ->assertSessionHasErrors(['product_image' => '写真か動画を選んでください。']);

        $this->assertSame(0, Product::count());
    }

    public function test_video_can_be_removed_and_files_are_deleted_with_the_item(): void
    {
        $farm = $this->makeFarm();
        $disk = Storage::disk(ImageStorage::DISK);
        $disk->put('product_videos/a.mp4', 'video');
        $disk->put('product_images/a.jpg', 'poster');
        $product = Product::create(['farm_id' => $farm->id, 'product_image' => '/uploads/product_images/a.jpg', 'product_video' => '/uploads/product_videos/a.mp4']);

        // 動画を外すと、一覧の写真は残って写真の項目になる
        $this->actingAs($this->admin, 'admins')
            ->put(route('admin.backend.products.update', $product->id), ['product_link' => '', 'remove_video' => '1'])
            ->assertSessionHasNoErrors();
        $this->assertNull($product->fresh()->product_video);
        $disk->assertMissing('product_videos/a.mp4');
        $disk->assertExists('product_images/a.jpg');

        // 削除すると、動画と写真のファイルも消える
        $disk->put('product_videos/b.mp4', 'video');
        $product->update(['product_video' => '/uploads/product_videos/b.mp4']);
        $this->actingAs($this->admin, 'admins')->delete(route('admin.backend.products.destroy', $product->id));
        $disk->assertMissing('product_videos/b.mp4');
        $disk->assertMissing('product_images/a.jpg');
    }

    public function test_farm_page_shows_the_poster_with_a_play_mark_and_passes_the_video_to_the_modal(): void
    {
        $farm = $this->makeFarm();
        Product::create(['farm_id' => $farm->id, 'product_name' => '冬も放牧', 'product_image' => '/uploads/product_images/a.jpg']);
        Product::create(['farm_id' => $farm->id, 'product_name' => '草を食べる', 'product_image' => '/uploads/product_images/b.jpg', 'product_video' => '/uploads/product_videos/b.mp4']);

        $html = $this->get(route('farm.show', $farm->id))
            ->assertOk()
            ->assertSee('alt="草を食べる(動画)"', false)
            ->assertSee('alt="冬も放牧"', false)
            ->assertSee('id="modal-video"', false)
            ->getContent();

        // 再生マークと動画のURLは、動画の1件だけ
        $this->assertSame(1, substr_count($html, 'class="gallery-play"'));
        $this->assertSame(1, substr_count($html, '\/uploads\/product_videos\/b.mp4'));
    }
}
