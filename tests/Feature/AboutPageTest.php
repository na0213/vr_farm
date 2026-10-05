<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * 運営者情報ページ(/about)。誰が・なぜやっているかを、話してもらった事実だけで書く。
 */
class AboutPageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_about_page_tells_who_runs_the_site_and_why(): void
    {
        $this->get(route('about.index'))
            ->assertOk()
            ->assertSeeText('運営者情報')
            ->assertSeeText('Natomi')
            ->assertSeeText('個人の趣味')
            ->assertSeeText('スーパー')                      // 始めたきっかけ
            ->assertSeeText('実際に訪ねた')                  // 牧場の選び方
            ->assertSee('info@natomi.work')                   // 連絡先のメール
            ->assertSee(route('contact.form'), false)        // 訂正・連絡はフォームへ
            ->assertSee('個人の趣味', false);                // meta description にも入る
    }

    public function test_about_page_has_decorative_walking_animals_that_screen_readers_skip(): void
    {
        $this->get(route('about.index'))
            ->assertOk()
            ->assertSee('class="walkers" aria-hidden="true"', false);
    }
}
