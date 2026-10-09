{{-- 日本地図(牧場検索)。登録のある都道府県に色をつけ、牧場の件数を丸で示す。押すと下の都道府県チェックに連動する(JS は farm/map.blade.php)。
     形は resources/data/japan-map.json(Natural Earth・パブリックドメインを簡略化。作り方は scripts/build-japan-map.py)。
     counts: ['北海道' => 2, ...](都道府県名 => 公開中の牧場の数)
     farms: 公開中の牧場(farmImages つき)。県を押したときに、その県の周りに浮かぶ牧場の丸と、押したときのポップアップに使う --}}
@props(['counts' => [], 'farms' => collect()])
@php
    $map = json_decode(file_get_contents(resource_path('data/japan-map.json')), true);
    [$vx, $vy, $vw, $vh] = $map['viewBox'];
    $inset = $map['inset'];
    $prefs = collect($map['prefectures']);
    $withFarm = $prefs->filter(fn ($p) => ($counts[$p['name']] ?? 0) > 0);
    $farmsByPrefecture = collect($farms)->groupBy('prefecture');
@endphp
@if ($withFarm->isNotEmpty())
    <div class="japan-map" data-japan-map data-vw="{{ $vw }}" data-vh="{{ $vh }}">
        <svg viewBox="{{ $vx }} {{ $vy }} {{ $vw }} {{ $vh }}" role="group" aria-label="日本地図。色のついた都道府県に、登録されている牧場があります" focusable="false">
            <rect class="japan-map-inset" x="{{ $inset['x'] }}" y="{{ $inset['y'] }}" width="{{ $inset['w'] }}" height="{{ $inset['h'] }}" rx="10" aria-hidden="true"/>
            <text class="japan-map-inset-label" x="{{ $inset['x'] + 10 }}" y="{{ $inset['y'] + $inset['h'] - 8 }}" aria-hidden="true">{{ $inset['label'] }}</text>
            {{-- 牧場のない都道府県 → 牧場のある都道府県(色のついたほうの輪郭が上になる) → 件数の丸 --}}
            @foreach ($prefs->diffKeys($withFarm) as $p)
                <path class="pref{{ $p['name'] === $inset['label'] ? ' is-inset' : '' }}" d="{{ $p['d'] }}" aria-hidden="true"/>
            @endforeach
            @foreach ($withFarm as $p)
                @php($n = $counts[$p['name']])
                <path class="pref has-farm{{ $p['name'] === $inset['label'] ? ' is-inset' : '' }}" d="{{ $p['d'] }}" data-prefecture="{{ $p['name'] }}" data-cx="{{ $p['cx'] }}" data-cy="{{ $p['cy'] }}" role="button" tabindex="0" aria-pressed="false"
                      aria-label="{{ $p['name'] }}の牧場 {{ $n }}件で絞り込む"><title>{{ $p['name'] }}({{ $n }}件)</title></path>
            @endforeach
            @foreach ($withFarm as $p)
                @php($n = $counts[$p['name']])
                <g class="pin" aria-hidden="true">
                    <circle cx="{{ $p['cx'] }}" cy="{{ $p['cy'] }}" r="{{ $n > 1 ? 11 : 8 }}"/>
                    @if ($n > 1)
                        <text x="{{ $p['cx'] }}" y="{{ $p['cy'] }}">{{ $n }}</text>
                    @endif
                </g>
            @endforeach
        </svg>
        {{-- 県を押すと、その県の周りに牧場の写真の丸が浮かぶ(位置は JS で決める)。丸を押すとポップアップ --}}
        <div class="japan-bubbles">
            @foreach ($withFarm as $p)
                <div class="japan-bubble-set" data-bubble-set="{{ $p['name'] }}" role="group" aria-label="{{ $p['name'] }}の牧場" hidden>
                    @foreach ($farmsByPrefecture->get($p['name'], collect()) as $farm)
                        @php($image = $farm->farmImages->first()?->image_path ?: asset('storage/noimage.jpg'))
                        <button type="button" class="japan-bubble" style="--i: {{ $loop->index }}"
                                data-farm-name="{{ $farm->farm_name }}" data-farm-prefecture="{{ $farm->prefecture }}"
                                data-farm-image="{{ $image }}" data-farm-url="{{ route('farm.show', $farm->id) }}"
                                aria-label="{{ $farm->farm_name }}の写真を見る">
                            <img src="{{ $image }}" alt="" loading="lazy" draggable="false">
                        </button>
                    @endforeach
                </div>
            @endforeach
        </div>
        <dialog class="farm-dialog" data-farm-dialog aria-labelledby="farm-dialog-name">
            <button type="button" class="item-dialog-close" data-farm-dialog-close aria-label="閉じる">&times;</button>
            <img data-farm-dialog-image src="" alt="">
            <div class="farm-dialog-body">
                <p class="farm-dialog-pref" data-farm-dialog-pref></p>
                <h3 id="farm-dialog-name" data-farm-dialog-name></h3>
                <a href="#" class="btn-butter" data-farm-dialog-link>牧場のページを見る</a>
            </div>
        </dialog>
        <p class="japan-map-status" data-japan-map-status aria-live="polite">
            <span data-japan-map-text>色のついた都道府県を押すと、その地域の牧場だけに絞り込めます。</span>
            <a href="#farm-results" class="japan-map-jump" data-japan-map-jump hidden>一覧を見る ↓</a>
            <button type="button" class="japan-map-reset" data-japan-map-reset hidden>地域の絞り込みをやめる</button>
        </p>
    </div>
@endif
