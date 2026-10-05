<x-top-layout>
    <x-slot name="title">牧場を探す</x-slot>
    <x-slot name="metaDescription">全国の放牧・平飼いなど、こだわりの育て方をする牧場を、地域・キーワード・動物の種類から探せます。</x-slot>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <!-- パンくずリストの表示 -->
    <nav aria-label="breadcrumb" class="mt-10 mb-10">
        {!! Breadcrumbs::render('farm.index') !!}
    </nav>
    <div class="wrap">
        <div class="container mx-auto px-4 py-6">
            <h1 class="text-xl font-bold mb-4 with-icon"><span class="wavy-underline">牧場検索</span></h1>

            <!-- 日本地図: 牧場のある都道府県に色がつく。押すと、その県の周りに牧場の写真の丸が浮かび(押すとポップアップ)、下の「都道府県」と一覧も連動して絞り込まれる -->
            <x-japan-map :counts="$prefectureCounts" :farms="$farms" />

            <!-- 検索フォーム(絞り込みはページ下のスクリプトで行う。静的サイトなのでサーバーでは検索しない) -->
            <form id="farm-search" action="{{ route('farm.index') }}" method="GET" class="mb-6">
                <!-- キーワード検索 -->
                <div class="mb-4">
                    <label for="keyword" class="block font-bold mb-2">キーワード検索</label>
                    <input type="text" id="keyword" name="keyword" class="w-full border border-gray-300 p-2 rounded">
                </div>

                <!-- 都道府県検索 -->
                <div class="mb-4">
                    <label class="block font-bold mb-2">都道府県</label>
                    @foreach ($prefectures as $prefecture)
                        <label class="inline-flex items-center mr-4">
                            <input class="checkbox" type="checkbox" name="prefectures[]" value="{{ $prefecture }}">
                            <span class="ml-2">{{ $prefecture }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- キーワード検索 -->
                <div class="mb-4">
                    <label class="block font-bold mb-2">キーワード</label>
                    @foreach ($keywords as $keyword)
                        <label class="inline-flex items-center mr-4">
                            <input class="checkbox" type="checkbox" name="keywords[]" value="{{ $keyword->id }}">
                            <span class="ml-2">{{ $keyword->keyword }}</span>
                        </label>
                    @endforeach
                </div>

                <!-- 種別検索 -->
                <div class="mb-4">
                    <label class="block font-bold mb-2">種別</label>
                    @foreach ($kinds as $kind)
                        <label class="inline-flex items-center mr-4">
                            <input class="checkbox" type="checkbox" name="kinds[]" value="{{ $kind->id }}">
                            <span class="ml-2">{{ $kind->kind }}</span>
                        </label>
                    @endforeach
                </div>
    
                <!-- 検索ボタン -->
                <div class="flex justify-center items-center">
                    <button type="submit" class="btn-primary" aria-label="検索を実行">
                        検索
                    </button>
                </div>
            </form>
    
            <!-- 検索結果 -->
            <ul id="farm-results" class="list-none space-y-4" aria-live="polite">
                @foreach ($farms as $farm)
                    @php
                        // ブラウザ側の絞り込みに使う値(キーワード検索は牧場名・キャッチコピー・都道府県・紹介文・キーワード・種別の部分一致)
                        $searchText = implode(' ', array_merge(
                            [$farm->farm_name, $farm->catchcopy, $farm->prefecture, strip_tags((string) $farm->theme)],
                            $farm->keywords->pluck('keyword')->all(),
                            $farm->kinds->pluck('kind')->all(),
                        ));
                    @endphp
                    <li class="card flex items-center rounded overflow-hidden shadow-lg bg-white p-4"
                        data-prefecture="{{ $farm->prefecture }}"
                        data-keywords="{{ $farm->keywords->pluck('id')->implode(',') }}"
                        data-kinds="{{ $farm->kinds->pluck('id')->implode(',') }}"
                        data-search="{{ $searchText }}">
                        <a href="{{ route('farm.show', ['id' => $farm->id]) }}" class="flex w-full">
                            <!-- 左側の画像 -->
                            @if($farm->farmImages->isNotEmpty())
                                <div class="farmimg w-1/3">
                                    <img src="{{ $farm->farmImages->first()->image_path }}" alt="{{ $farm->farm_name }}" loading="lazy">
                                </div>
                            @else
                                <div class="farmimg w-1/3">
                                    <img src="../storage/noimage.jpg" alt="No image available">
                                </div>
                            @endif
                            <!-- 右側の詳細情報 -->
                            <div class="ml-4 flex-1">
                                <p class="ttl font-bold text-lg">{{ $farm->farm_name }}</p>
                                <p class="pref text-gray-600">{{ $farm->prefecture }}</p>
                                <div class="icon my-2">
                                    @foreach ($farm->kinds as $kind)
                                        @switch($kind->id)
                                            @case(1)
                                                <img src="../storage/cow.png" alt="Cow" class="inline-block w-6 h-6 mr-2">
                                                @break
                                            @case(2)
                                                <img src="../storage/ushi.png" alt="Ushi" class="inline-block w-6 h-6 mr-2">
                                                @break
                                            @case(3)
                                                <img src="../storage/bird.png" alt="Bird" class="inline-block w-6 h-6 mr-2">
                                                @break
                                            @case(4)
                                                <img src="../storage/pig.png" alt="Pig" class="inline-block w-6 h-6 mr-2">
                                                @break
                                        @endswitch
                                    @endforeach
                                </div>
                                @if (!empty($farm->catchcopy))
                                    <p class="text-gray-700">{{ $farm->catchcopy }}</p>
                                @endif
                                <p class="tag text-gray-500 mt-2">
                                    @foreach ($farm->keywords as $keyword)
                                        <span class="py-1 text-xs font-semibold text-gray-700 mr-2 mb-2">#{{ $keyword->keyword }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </a>
                    </li>
                @endforeach
            </ul>
            <p id="farm-empty" class="text-gray-500" @if ($farms->isNotEmpty()) hidden @endif>該当する牧場が見つかりませんでした。</p>
    </div>
    <script>
        // 牧場の絞り込み(条件はすべて満たすものを表示。チェックは同じ欄の中のどれか1つに当てはまればよい)
        (() => {
            const form = document.getElementById('farm-search');
            const items = Array.from(document.querySelectorAll('#farm-results > li'));
            const empty = document.getElementById('farm-empty');
            const normalize = (s) => (s || '').normalize('NFKC').toLowerCase();
            const checked = (name) => Array.from(form.querySelectorAll(`input[name="${name}"]:checked`)).map((el) => el.value);

            // 日本地図との連動: チェックの状態を地図に映し、地図を押したらチェックを入れ替えて検索する
            const map = document.querySelector('[data-japan-map]');
            const prefectureBoxes = Array.from(form.querySelectorAll('input[name="prefectures[]"]'));
            const syncMap = (shown) => {
                if (!map) return;
                const chosen = prefectureBoxes.filter((el) => el.checked).map((el) => el.value);
                map.querySelectorAll('path[data-prefecture]').forEach((shape) => {
                    const on = chosen.includes(shape.dataset.prefecture);
                    shape.classList.toggle('is-selected', on);
                    shape.setAttribute('aria-pressed', on ? 'true' : 'false');
                });
                const text = map.querySelector('[data-japan-map-text]');
                if (chosen.length === 0) {
                    text.textContent = '色のついた都道府県を押すと、その地域の牧場だけに絞り込めます。';
                } else if (shown !== undefined) {
                    text.textContent = `${chosen.join('・')}の牧場を表示しています(${shown}件)`;
                }
                map.querySelector('[data-japan-map-jump]').hidden = chosen.length === 0;
                map.querySelector('[data-japan-map-reset]').hidden = chosen.length === 0;
            };

            const apply = () => {
                const keyword = normalize(form.keyword.value.trim());
                const prefectures = checked('prefectures[]');
                const keywords = checked('keywords[]');
                const kinds = checked('kinds[]');
                const hasAny = (list, wanted) => wanted.length === 0 || list.split(',').some((id) => wanted.includes(id));
                let shown = 0;

                items.forEach((li) => {
                    const match = (keyword === '' || normalize(li.dataset.search).includes(keyword))
                        && (prefectures.length === 0 || prefectures.includes(li.dataset.prefecture))
                        && hasAny(li.dataset.keywords, keywords)
                        && hasAny(li.dataset.kinds, kinds);
                    li.style.display = match ? '' : 'none'; // .flex が hidden 属性より優先されるため style で消す
                    if (match) shown++;
                });
                empty.hidden = shown > 0;
                syncMap(shown);
            };

            // URL の条件(?keyword=...&kinds[]=1)をフォームに反映する(共有されたリンクでも同じ結果になるように)
            const params = new URLSearchParams(location.search);
            form.keyword.value = params.get('keyword') || '';
            ['prefectures[]', 'keywords[]', 'kinds[]'].forEach((name) => {
                const values = params.getAll(name);
                form.querySelectorAll(`input[name="${name}"]`).forEach((el) => { el.checked = values.includes(el.value); });
            });
            apply();

            if (map) {
                const svg = map.querySelector('svg');
                const sets = Array.from(map.querySelectorAll('[data-bubble-set]'));
                const dialog = map.querySelector('[data-farm-dialog]');
                const vw = Number(map.dataset.vw);
                const vh = Number(map.dataset.vh);
                let active = null; // 今、牧場の丸を出している都道府県

                const hideBubbles = () => {
                    sets.forEach((set) => { set.hidden = true; set.classList.remove('is-open'); });
                    map.classList.remove('has-bubbles');
                    map.querySelectorAll('path.is-active').forEach((el) => el.classList.remove('is-active'));
                    active = null;
                };

                // 県の周りに丸を並べる。県の外形のすぐ外側の、地図の中におさまる場所を、
                // 海側(地図の中心から見て外向き)に近いものから順に使い、丸どうしが重ならないようにする
                const layout = (set, shape) => {
                    const box = map.getBoundingClientRect();
                    const frame = svg.getBoundingClientRect();
                    const ox = frame.left - box.left;
                    const oy = frame.top - box.top;
                    const k = frame.width / vw;
                    const cx = Number(shape.dataset.cx);
                    const cy = Number(shape.dataset.cy);
                    const px = cx * k + ox;
                    const py = cy * k + oy;
                    const items = Array.from(set.children);
                    const d = items[0].offsetWidth;
                    const half = d / 2 + 2;
                    const out = Math.atan2(cy - vh * 0.5, cx - vw * 0.5);
                    const bb = shape.getBBox();
                    // 県の中心から角度 a の向きに、外形の縁まであるきょり(地図上の長さ)
                    const reach = (a) => {
                        const dx = Math.cos(a);
                        const dy = Math.sin(a);
                        const tx = dx > 0 ? (bb.x + bb.width - cx) / dx : dx < 0 ? (bb.x - cx) / dx : Infinity;
                        const ty = dy > 0 ? (bb.y + bb.height - cy) / dy : dy < 0 ? (bb.y - cy) / dy : Infinity;
                        return Math.min(tx, ty);
                    };

                    const spots = [];
                    for (let ring = 0; ring < 4; ring++) {
                        for (let deg = 0; deg < 360; deg += 10) {
                            const a = (deg * Math.PI) / 180;
                            const r = reach(a) * k + d / 2 + 4 + ring * (d + 8);
                            const x = px + r * Math.cos(a);
                            const y = py + r * Math.sin(a);
                            if (x < half || x > frame.width - half || y < oy + half || y > oy + frame.height - half) continue;
                            spots.push({ x, y, ring, away: Math.abs(Math.atan2(Math.sin(a - out), Math.cos(a - out))) });
                        }
                    }
                    spots.sort((a, b) => a.ring - b.ring || a.away - b.away);

                    const placed = [];
                    items.forEach((item) => {
                        const spot = spots.find((s) => placed.every((p) => Math.hypot(p.x - s.x, p.y - s.y) >= d + 4))
                            || { x: Math.min(Math.max(px, half), frame.width - half), y: Math.min(Math.max(py, oy + half), oy + frame.height - half) };
                        placed.push(spot);
                        item.style.setProperty('--x', `${spot.x}px`);
                        item.style.setProperty('--y', `${spot.y}px`);
                    });
                };

                const showBubbles = (name, focus) => {
                    hideBubbles();
                    const set = sets.find((el) => el.dataset.bubbleSet === name);
                    const shape = map.querySelector(`path[data-prefecture="${name}"]`);
                    if (!set || !shape) return;
                    active = name;
                    shape.classList.add('is-active');
                    map.classList.add('has-bubbles');
                    set.hidden = false;
                    layout(set, shape);
                    void set.offsetWidth; // 出す前の状態(小さく・透明)を確定させてから、ふわっと出す
                    set.classList.add('is-open');
                    if (focus) set.firstElementChild.focus({ preventScroll: true });
                };

                // 県を押す: 選んでいなければ「選ぶ+丸を出す」。選んであって丸が出ていなければ「丸を出す」。丸が出ていれば「選びをやめる」
                const toggle = (name, byKey) => {
                    const box = prefectureBoxes.find((el) => el.value === name);
                    if (!box) return;
                    if (box.checked && active !== name) {
                        showBubbles(name, byKey);
                        return;
                    }
                    box.checked = !box.checked;
                    form.requestSubmit();
                    if (box.checked) showBubbles(name, byKey);
                    else hideBubbles();
                };
                map.querySelectorAll('path[data-prefecture]').forEach((shape) => {
                    shape.addEventListener('click', () => toggle(shape.dataset.prefecture, false));
                    shape.addEventListener('keydown', (event) => {
                        if (event.key === 'Enter' || event.key === ' ') {
                            event.preventDefault();
                            toggle(shape.dataset.prefecture, true);
                        }
                    });
                });
                map.querySelector('[data-japan-map-reset]').addEventListener('click', () => {
                    prefectureBoxes.forEach((el) => { el.checked = false; });
                    hideBubbles();
                    form.requestSubmit();
                });

                // 丸を押す: 牧場の写真と名前をポップアップで見せる
                map.querySelectorAll('.japan-bubble').forEach((bubble) => {
                    bubble.addEventListener('click', () => {
                        dialog.querySelector('[data-farm-dialog-image]').src = bubble.dataset.farmImage;
                        dialog.querySelector('[data-farm-dialog-image]').alt = bubble.dataset.farmName;
                        dialog.querySelector('[data-farm-dialog-pref]').textContent = bubble.dataset.farmPrefecture;
                        dialog.querySelector('[data-farm-dialog-name]').textContent = bubble.dataset.farmName;
                        dialog.querySelector('[data-farm-dialog-link]').href = bubble.dataset.farmUrl;
                        dialog.showModal();
                    });
                });
                dialog.querySelector('[data-farm-dialog-close]').addEventListener('click', () => dialog.close());
                dialog.addEventListener('click', (event) => { if (event.target === dialog) dialog.close(); });

                // 地図の外や空いているところを押す/Esc で、丸をしまう(選びはそのまま)
                document.addEventListener('click', (event) => {
                    if (!active || event.target.closest('.japan-bubble, path[data-prefecture], [data-farm-dialog]')) return;
                    hideBubbles();
                });
                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && active && !dialog.open) hideBubbles();
                });
                window.addEventListener('resize', () => {
                    const set = active && sets.find((el) => el.dataset.bubbleSet === active);
                    if (set) layout(set, map.querySelector(`path[data-prefecture="${active}"]`));
                });
                // チェックボックスを直接変えたときも、地図の色を合わせる(一覧は「検索」を押したときに変わる)
                prefectureBoxes.forEach((el) => el.addEventListener('change', () => syncMap()));
            }

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                const query = new URLSearchParams(new FormData(form));
                if (!form.keyword.value.trim()) query.delete('keyword');
                history.replaceState(null, '', query.toString() ? `?${query}` : location.pathname);
                apply();
            });
        })();
    </script>
</x-top-layout>
