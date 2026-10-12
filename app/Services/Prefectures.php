<?php

namespace App\Services;

use Illuminate\Support\Collection;

/**
 * 都道府県の一般的な並び(北海道から沖縄まで。JIS X 0401 の都道府県コード順)。
 */
class Prefectures
{
    public const ORDER = [
        '北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県',
        '茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県',
        '新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県',
        '三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県',
        '鳥取県', '島根県', '岡山県', '広島県', '山口県',
        '徳島県', '香川県', '愛媛県', '高知県',
        '福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県',
    ];

    /**
     * 都道府県名を、北海道から沖縄の順に並べる。一覧にない名前(表記ゆれなど)は、最後に元の順で並べる。
     *
     * @param  iterable<string>  $names
     * @return Collection<int, string>
     */
    public static function sort(iterable $names): Collection
    {
        $rank = array_flip(self::ORDER);

        return collect($names)
            ->values()
            ->sortBy(fn (string $name, int $index) => [$rank[$name] ?? count($rank), $index])
            ->values();
    }
}
