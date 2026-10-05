<?php

namespace Tests\Feature;

use App\Models\Farm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 牧場検索の日本地図: 公開中の牧場がある都道府県に色がつき、件数が出る。
 */
class JapanMapTest extends TestCase
{
    use RefreshDatabase;

    private function makeFarm(string $name, string $prefecture, bool $published = true): Farm
    {
        $farm = new Farm(['farm_name' => $name, 'catchcopy' => 'c', 'prefecture' => $prefecture, 'address' => 'a', 'theme' => 't']);
        $farm->is_published = $published;
        $farm->save();

        return $farm;
    }

    public function test_map_data_has_all_47_prefectures(): void
    {
        $map = json_decode(file_get_contents(resource_path('data/japan-map.json')), true);
        $names = array_column($map['prefectures'], 'name');

        $this->assertCount(47, $names);
        $this->assertCount(47, array_unique($names));
        foreach (['北海道', '東京都', '大阪府', '京都府', '沖縄県', '宮崎県'] as $name) {
            $this->assertContains($name, $names);
        }
        foreach ($map['prefectures'] as $prefecture) {
            $this->assertNotSame('', $prefecture['d'], $prefecture['name'] . ' の形が空');
        }
    }

    public function test_prefectures_with_published_farms_are_colored_and_show_the_count(): void
    {
        $this->makeFarm('A牧場', '北海道');
        $this->makeFarm('B牧場', '北海道');
        $this->makeFarm('C牧場', '青森県');
        $this->makeFarm('非公開の牧場', '宮崎県', false);

        $html = $this->get(route('farm.index'))->assertOk()
            ->assertSee('class="japan-map"', false)
            ->assertSee('data-prefecture="北海道"', false)
            ->assertSee('北海道の牧場 2件で絞り込む')
            ->assertSee('data-prefecture="青森県"', false)
            ->assertSee('青森県の牧場 1件で絞り込む')
            // 牧場のない都道府県と、非公開の牧場だけの都道府県は、押せない(色もつかない)
            ->assertDontSee('data-prefecture="東京都"', false)
            ->assertDontSee('data-prefecture="宮崎県"', false)
            ->getContent();

        $this->assertSame(2, substr_count($html, 'class="pref has-farm'));
    }

    public function test_map_is_hidden_when_there_are_no_published_farms(): void
    {
        $this->makeFarm('非公開の牧場', '北海道', false);

        $this->get(route('farm.index'))->assertOk()->assertDontSee('class="japan-map"', false);
    }
}
