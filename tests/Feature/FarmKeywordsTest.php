<?php

namespace Tests\Feature;

use App\Models\Farm;
use App\Models\Keyword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 牧場のキーワード(こだわり): 付いているときだけ、牧場ページに「こだわり」欄を出す。
 */
class FarmKeywordsTest extends TestCase
{
    use RefreshDatabase;

    private function makeFarm(string $name): Farm
    {
        $farm = new Farm(['farm_name' => $name, 'catchcopy' => 'c', 'prefecture' => '北海道', 'address' => 'a', 'theme' => 't']);
        $farm->is_published = true;
        $farm->save();

        return $farm;
    }

    public function test_farm_page_shows_the_keywords_row_only_when_the_farm_has_keywords(): void
    {
        $with = $this->makeFarm('付きの牧場');
        $with->keywords()->attach(Keyword::create(['keyword' => '放牧'])->id);
        $without = $this->makeFarm('無しの牧場');

        $this->get(route('farm.show', $with->id))->assertOk()->assertSee('#放牧')->assertSee('>こだわり</th>', false);
        $this->get(route('farm.show', $without->id))->assertOk()->assertDontSee('>こだわり</th>', false);
    }
}
