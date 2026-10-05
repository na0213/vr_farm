<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Farm;
use App\Services\ImageStorage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * 静的サイト(Cloudflare 配信)への移行で足した仕組みのテスト。
 * 書き出し(site:export)、画像の保存先(public/uploads)、S3 からの取り込み。
 */
class StaticSiteTest extends TestCase
{
    use RefreshDatabase;

    private string $out;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->out = sys_get_temp_dir().'/farm360-export-test-'.uniqid();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->out);
        parent::tearDown();
    }

    private function makeFarm(string $name, bool $published, string $theme = 'テーマ'): Farm
    {
        $farm = new Farm([
            'farm_name' => $name,
            'catchcopy' => 'キャッチ',
            'prefecture' => '北海道',
            'address' => '住所',
            'theme' => $theme,
        ]);
        $farm->is_published = $published;
        $farm->save();

        return $farm;
    }

    private function makeArticle(string $title, bool $published, string $images = '[]', string $content = '<p>本文</p>'): Article
    {
        $article = new Article;
        $article->title = $title;
        $article->article_content = $content;
        $article->article_images = $images;
        $article->is_published = $published;
        $article->save();

        return $article;
    }

    public function test_export_writes_public_pages_only(): void
    {
        if (! File::exists(public_path('build/manifest.json'))) {
            $this->markTestSkipped('npm run build が必要です');
        }

        $open = $this->makeFarm('公開牧場', true);
        $closed = $this->makeFarm('非公開牧場', false);
        $published = $this->makeArticle('公開記事', true);
        $draft = $this->makeArticle('下書き記事', false);
        config(['services.turnstile.site_key' => 'test-site-key']);

        $this->artisan('site:export', ['--out' => $this->out])->assertSuccessful();

        foreach (['index.html', 'farm/map.html', 'products.html', 'kodawari.html', 'about.html', 'contact.html', 'sitemap.xml', '404.html'] as $file) {
            $this->assertFileExists("{$this->out}/{$file}");
        }
        $this->assertFileExists("{$this->out}/farm/{$open->id}.html");
        $this->assertFileDoesNotExist("{$this->out}/farm/{$closed->id}.html");
        $this->assertFileExists("{$this->out}/article/{$published->id}.html");
        $this->assertFileDoesNotExist("{$this->out}/article/{$draft->id}.html");

        // PHP の入口は含めない。静的な画像はそのまま含める
        $this->assertFileDoesNotExist("{$this->out}/index.php");
        $this->assertFileExists("{$this->out}/storage/favicon.png");

        // 本番のURLで書き出す(canonical・サイトマップ)
        $farmHtml = File::get("{$this->out}/farm/{$open->id}.html");
        $this->assertStringContainsString('<link rel="canonical" href="https://www.farm360.jp/farm/'.$open->id.'">', $farmHtml);
        $this->assertStringNotContainsString('csrf-token', $farmHtml);
        $this->assertStringContainsString('https://www.farm360.jp/article/'.$published->id, File::get("{$this->out}/sitemap.xml"));

        $this->assertStringContainsString('/animal-welfare /kodawari 301', File::get("{$this->out}/_redirects"));
        $this->assertStringNotContainsString('noindex', File::get("{$this->out}/_headers"));
    }

    public function test_preview_export_is_not_indexed(): void
    {
        if (! File::exists(public_path('build/manifest.json'))) {
            $this->markTestSkipped('npm run build が必要です');
        }

        $this->artisan('site:export', ['--out' => $this->out, '--url' => 'https://farm360.example.workers.dev'])->assertSuccessful();

        $this->assertStringContainsString('X-Robots-Tag: noindex', File::get("{$this->out}/_headers"));
        $this->assertStringContainsString('https://farm360.example.workers.dev/', File::get("{$this->out}/index.html"));
    }

    public function test_production_export_requires_the_turnstile_site_key(): void
    {
        config(['services.turnstile.site_key' => '']);

        $this->artisan('site:export', ['--out' => $this->out])->assertFailed();
        $this->assertDirectoryDoesNotExist($this->out);
    }

    public function test_export_refuses_to_overwrite_the_project_folder(): void
    {
        $this->artisan('site:export', ['--out' => base_path()])->assertFailed();
        $this->artisan('site:export', ['--out' => public_path()])->assertFailed();
    }

    public function test_farm_search_page_carries_data_for_browser_filtering(): void
    {
        $farm = $this->makeFarm('しあわせ牧場', true, '<p>放牧の<strong>豚</strong></p>');

        $this->get(route('farm.index', ['keyword' => '存在しない']))
            ->assertOk()
            ->assertSee('しあわせ牧場') // サーバーでは絞り込まない(ブラウザ側で絞り込む)
            ->assertSee('data-prefecture="北海道"', false)
            ->assertSee('放牧の豚', false);

        $this->assertNotNull($farm);
    }

    public function test_contact_page_sends_to_the_worker(): void
    {
        $this->get(route('contact.form'))
            ->assertOk()
            ->assertSee("fetch('/api/contact'", false)
            ->assertDontSee('name="_token"', false);

        $this->post('/contact/confirm')->assertNotFound(); // 旧: サーバーで確認・送信していた
    }

    public function test_uploaded_images_are_stored_under_uploads(): void
    {
        Storage::fake(ImageStorage::DISK);

        $url = ImageStorage::storeResized(UploadedFile::fake()->image('a.png', 2400, 1200), 'farms/a.jpg');

        $this->assertSame('/uploads/farms/a.jpg', $url);
        Storage::disk(ImageStorage::DISK)->assertExists('farms/a.jpg');

        ImageStorage::delete($url);
        Storage::disk(ImageStorage::DISK)->assertMissing('farms/a.jpg');
    }

    public function test_only_site_images_can_be_deleted(): void
    {
        $this->assertSame('farms/a.jpg', ImageStorage::keyFromUrl('/uploads/farms/a.jpg'));
        $this->assertSame('farms/a.jpg', ImageStorage::keyFromUrl('https://www.farm360.jp/uploads/farms/a.jpg'));
        $this->assertNull(ImageStorage::keyFromUrl('/storage/favicon.png'));
        $this->assertNull(ImageStorage::keyFromUrl('/uploads/../.env'));
        $this->assertNull(ImageStorage::keyFromUrl(null));
    }

    public function test_s3_images_are_imported_and_urls_rewritten(): void
    {
        Storage::fake(ImageStorage::DISK);
        Http::fake([
            'bucket.s3.ap-northeast-1.amazonaws.com/*' => Http::response('jpeg-bytes'),
        ]);

        $s3 = 'https://bucket.s3.ap-northeast-1.amazonaws.com';
        $farm = $this->makeFarm('牧場', true);
        $farm->vr = "{$s3}/farm_vr/vr.jpg";
        $farm->save();
        $article = $this->makeArticle(
            '記事',
            true,
            json_encode(["{$s3}/article_images/a.jpg"]),
            "<p><img src=\"{$s3}/article_images/body.jpg\"></p>",
        );

        $this->artisan('images:import-from-s3')->assertSuccessful();

        $this->assertSame('/uploads/farm_vr/vr.jpg', $farm->fresh()->vr);
        $article = $article->fresh();
        $this->assertSame(['/uploads/article_images/a.jpg'], json_decode($article->article_images, true));
        $this->assertSame('<p><img src="/uploads/article_images/body.jpg"></p>', $article->article_content);

        Storage::disk(ImageStorage::DISK)->assertExists(['farm_vr/vr.jpg', 'article_images/a.jpg', 'article_images/body.jpg']);
        $this->assertSame('jpeg-bytes', Storage::disk(ImageStorage::DISK)->get('farm_vr/vr.jpg'));
    }

    public function test_s3_import_keeps_url_when_download_fails(): void
    {
        Storage::fake(ImageStorage::DISK);
        Http::fake(['*' => Http::response('', 403)]);

        $farm = $this->makeFarm('牧場', true);
        DB::table('farms')->where('id', $farm->id)->update(['vr' => 'https://bucket.s3.amazonaws.com/farm_vr/missing.jpg']);

        $this->artisan('images:import-from-s3')->assertFailed();

        $this->assertSame('https://bucket.s3.amazonaws.com/farm_vr/missing.jpg', $farm->fresh()->vr);
    }

    public function test_s3_import_dry_run_changes_nothing(): void
    {
        Storage::fake(ImageStorage::DISK);
        Http::fake();

        $farm = $this->makeFarm('牧場', true);
        DB::table('farms')->where('id', $farm->id)->update(['vr' => 'https://bucket.s3.amazonaws.com/farm_vr/vr.jpg']);

        $this->artisan('images:import-from-s3', ['--dry-run' => true])->assertSuccessful();

        $this->assertSame('https://bucket.s3.amazonaws.com/farm_vr/vr.jpg', $farm->fresh()->vr);
        Http::assertNothingSent();
    }
}
