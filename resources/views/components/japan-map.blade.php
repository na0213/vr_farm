{{-- 日本地図(牧場検索)。登録のある都道府県に色をつけ、牧場の件数を丸で示す。押すと下の都道府県チェックに連動する(JS は farm/map.blade.php)。
     形は resources/data/japan-map.json(Natural Earth・パブリックドメインを簡略化。作り方は scripts/build-japan-map.py)。
     counts: ['北海道' => 2, ...](都道府県名 => 公開中の牧場の数) --}}
@props(['counts' => []])
@php
    $map = json_decode(file_get_contents(resource_path('data/japan-map.json')), true);
    [$vx, $vy, $vw, $vh] = $map['viewBox'];
    $inset = $map['inset'];
    $prefs = collect($map['prefectures']);
    $withFarm = $prefs->filter(fn ($p) => ($counts[$p['name']] ?? 0) > 0);
@endphp
@if ($withFarm->isNotEmpty())
    <div class="japan-map" data-japan-map>
        <svg viewBox="{{ $vx }} {{ $vy }} {{ $vw }} {{ $vh }}" role="group" aria-label="日本地図。色のついた都道府県に、登録されている牧場があります" focusable="false">
            <rect class="japan-map-inset" x="{{ $inset['x'] }}" y="{{ $inset['y'] }}" width="{{ $inset['w'] }}" height="{{ $inset['h'] }}" rx="10" aria-hidden="true"/>
            <text class="japan-map-inset-label" x="{{ $inset['x'] + 10 }}" y="{{ $inset['y'] + $inset['h'] - 8 }}" aria-hidden="true">{{ $inset['label'] }}</text>
            {{-- 牧場のない都道府県 → 牧場のある都道府県(色のついたほうの輪郭が上になる) → 件数の丸 --}}
            @foreach ($prefs->diffKeys($withFarm) as $p)
                <path class="pref{{ $p['name'] === $inset['label'] ? ' is-inset' : '' }}" d="{{ $p['d'] }}" aria-hidden="true"/>
            @endforeach
            @foreach ($withFarm as $p)
                @php($n = $counts[$p['name']])
                <path class="pref has-farm{{ $p['name'] === $inset['label'] ? ' is-inset' : '' }}" d="{{ $p['d'] }}" data-prefecture="{{ $p['name'] }}" role="button" tabindex="0" aria-pressed="false"
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
        <p class="japan-map-status" data-japan-map-status aria-live="polite">
            <span data-japan-map-text>色のついた都道府県を押すと、その地域の牧場だけに絞り込めます。</span>
            <a href="#farm-results" class="japan-map-jump" data-japan-map-jump hidden>一覧を見る ↓</a>
            <button type="button" class="japan-map-reset" data-japan-map-reset hidden>地域の絞り込みをやめる</button>
        </p>
    </div>
@endif
