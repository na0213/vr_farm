<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * 記事の下書きには、書き足す場所に目印(<mark>【ここに書く:…】</mark>)を入れておく。
 * 目印が残ったまま、公開サイトに書き出されないようにする。
 */
class DraftMarkerTest extends TestCase
{
    use RefreshDatabase;

    private string $out;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->out = sys_get_temp_dir().'/farm360-marker-test-'.uniqid();
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->out);
        parent::tearDown();
    }

    private function makeArticle(string $title, bool $published, string $content): Article
    {
        $article = new Article;
        $article->title = $title;
        $article->article_content = $content;
        $article->article_images = '[]';
        $article->is_published = $published;
        $article->save();

        return $article;
    }

    public function test_article_knows_when_it_still_has_a_marker(): void
    {
        $with = new Article(['article_content' => '<p>本文</p><p><mark>【ここに書く:着いて最初に思ったこと】</mark></p>']);
        $this->assertTrue($with->hasDraftMarker());

        // 目印の書き方が少し違っても(属性・空白・改行)見逃さない
        $this->assertTrue((new Article(['article_content' => "<mark class=\"x\">\n 【確認してから書く:乳量】</mark>"]))->hasDraftMarker());
    }

    public function test_normal_text_is_not_a_marker(): void
    {
        $this->assertFalse((new Article(['article_content' => '<p>【お知らせ】普通の本文です。</p>']))->hasDraftMarker());
        $this->assertFalse((new Article(['article_content' => '<p><mark>強調</mark>したい言葉</p>']))->hasDraftMarker());
        $this->assertFalse((new Article(['article_content' => null]))->hasDraftMarker());
    }

    public function test_export_refuses_when_a_published_article_still_has_a_marker(): void
    {
        $this->makeArticle('書きかけの記事', true, '<p><mark>【ここに書く:感想】</mark></p>');

        $this->artisan('site:export', ['--out' => $this->out])
            ->expectsOutputToContain('書きかけの記事')
            ->assertFailed();

        $this->assertDirectoryDoesNotExist($this->out);
    }

    public function test_export_does_not_mind_a_marker_in_an_unpublished_draft(): void
    {
        if (! File::exists(public_path('build/manifest.json'))) {
            $this->markTestSkipped('npm run build が必要です');
        }

        $this->makeArticle('下書き', false, '<p><mark>【ここに書く:感想】</mark></p>');

        $this->artisan('site:export', ['--out' => $this->out, '--url' => 'https://farm360.example.workers.dev'])
            ->assertSuccessful();
    }
}
