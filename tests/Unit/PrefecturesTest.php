<?php

namespace Tests\Unit;

use App\Services\Prefectures;
use PHPUnit\Framework\TestCase;

class PrefecturesTest extends TestCase
{
    public function test_order_has_all_47_prefectures_without_duplicates(): void
    {
        $this->assertCount(47, Prefectures::ORDER);
        $this->assertCount(47, array_unique(Prefectures::ORDER));
        $this->assertSame('北海道', Prefectures::ORDER[0]);
        $this->assertSame('沖縄県', Prefectures::ORDER[46]);
    }

    public function test_sorts_from_hokkaido_to_okinawa_not_in_registration_order(): void
    {
        $sorted = Prefectures::sort(['宮崎県', '千葉県', '北海道', '青森県', '沖縄県', '東京都']);

        $this->assertSame(['北海道', '青森県', '千葉県', '東京都', '宮崎県', '沖縄県'], $sorted->all());
    }

    public function test_unknown_names_go_last_in_their_original_order(): void
    {
        $sorted = Prefectures::sort(['不明B', '大阪府', '不明A', '北海道']);

        $this->assertSame(['北海道', '大阪府', '不明B', '不明A'], $sorted->all());
    }
}
