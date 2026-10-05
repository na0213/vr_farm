<x-top-layout>
    <x-slot name="title">お取り寄せ</x-slot>
    <x-slot name="metaDescription">運営者が自分で買って撮影した、牧場の商品を牧場ごとに紹介します。食べた感想と、販売ページへのリンクつき。</x-slot>

    <div class="container mx-auto px-4 py-12 max-w-5xl">
        <div class="note-title">
            <p class="wavy-underline">お取り寄せ</p>
        </div>
        <p class="text-center text-stone-600 mt-6 mb-10 leading-relaxed">
            運営者が自分で買って、撮影した商品を、牧場ごとに紹介します。<br>
            販売ページがあるものは、リンクからお取り寄せできます。
        </p>

        @if ($farms->isEmpty())
            <div class="text-center py-16">
                <p class="text-stone-500 mb-6">商品情報はただいま準備中です。もうしばらくお待ちください。</p>
                <a href="{{ route('farm.index') }}" class="btn-butter">牧場を探してみる</a>
            </div>
        @else
            {{-- 牧場が2つ以上あるときだけ、牧場名で絞り込むボタンを出す(JS が動いたときだけ表示) --}}
            @if ($farms->count() > 1)
                <div class="farm-chips" data-farm-chips role="group" aria-label="牧場で絞り込む" hidden>
                    <button type="button" class="farm-chip" data-farm="" aria-pressed="true">すべて</button>
                    @foreach ($farms as $farm)
                        <button type="button" class="farm-chip" data-farm="{{ $farm->id }}" aria-pressed="false">{{ $farm->farm_name }}<span class="farm-chip-count">{{ $farm->purchasedItems->count() }}</span></button>
                    @endforeach
                </div>
            @endif

            @foreach ($farms as $farm)
                @php
                    $items = $farm->purchasedItems;
                    // 牧場ごとにまず見せるのは、新しい4件(登録順の最後の4件)。古いぶんは「すべて見る」で出す
                    $extra = max(0, $items->count() - 4);
                @endphp
                <section class="mb-16 product-section{{ $extra ? ' is-collapsed' : '' }}" data-farm-section="{{ $farm->id }}" data-extra="{{ $extra }}" aria-labelledby="farm-{{ $farm->id }}">
                    <h2 id="farm-{{ $farm->id }}" class="mb-6 flex flex-wrap items-baseline gap-x-3 border-b border-stone-200 pb-3">
                        <span class="text-xl font-bold text-stone-800">{{ $farm->farm_name }}</span>
                        <span class="text-sm text-stone-500">{{ $farm->prefecture }}</span>
                    </h2>
                    <div class="grid product-grid grid-stagger">
                        @foreach ($items as $item)
                            <x-purchased-item-card :item="$item" :class="$loop->index < $extra ? 'product-extra' : ''" />
                        @endforeach
                    </div>
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-x-6 gap-y-3">
                        @if ($extra > 0)
                            {{-- JS が動くときはこのページで全件を出し、動かないときは牧場ページ(全件の横スライド)へ --}}
                            <a href="{{ route('farm.show', $farm->id) }}#products" class="product-more btn-butter" data-farm-select="{{ $farm->id }}">この牧場の商品をすべて見る({{ $items->count() }}件)</a>
                        @endif
                        <a href="{{ route('farm.show', $farm->id) }}" class="btn-link text-sm hover:underline ml-auto">{{ $farm->farm_name }}のページへ →</a>
                    </div>
                </section>
            @endforeach
        @endif

        <div class="text-center mt-14 text-sm text-stone-500 leading-relaxed">
            写真と感想は、運営者が自分で購入したときのものです。<br>
            価格・在庫は販売ページでご確認ください。
        </div>
    </div>

    @if ($farms->count() > 1)
    <script>
        // 牧場名のボタンで絞り込む。1つ選ぶと、その牧場の商品を全件出す(?farm=… の URL も有効)
        (function () {
            const chips = document.querySelector('[data-farm-chips]');
            if (!chips) return;
            const buttons = Array.from(chips.querySelectorAll('.farm-chip'));
            const sections = Array.from(document.querySelectorAll('[data-farm-section]'));
            const ids = buttons.map(function (button) { return button.dataset.farm; });
            const smooth = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function select(id) {
                buttons.forEach(function (button) {
                    button.setAttribute('aria-pressed', button.dataset.farm === id ? 'true' : 'false');
                });
                sections.forEach(function (section) {
                    section.hidden = id !== '' && section.dataset.farmSection !== id;
                    section.classList.toggle('is-collapsed', id === '' && Number(section.dataset.extra) > 0);
                });
                history.replaceState(null, '', id ? '?farm=' + encodeURIComponent(id) : location.pathname);
            }

            chips.hidden = false;
            buttons.forEach(function (button) {
                button.addEventListener('click', function () { select(button.dataset.farm); });
            });
            // 「この牧場の商品をすべて見る」: 絞り込みに切り替えて、ボタンの列まで戻る
            document.querySelectorAll('[data-farm-select]').forEach(function (link) {
                link.addEventListener('click', function (event) {
                    event.preventDefault();
                    const id = link.dataset.farmSelect;
                    select(id);
                    buttons[ids.indexOf(id)].focus({ preventScroll: true });
                    chips.scrollIntoView({ block: 'start', behavior: smooth ? 'smooth' : 'auto' });
                });
            });

            const wanted = new URLSearchParams(location.search).get('farm');
            if (wanted && ids.indexOf(wanted) > 0) select(wanted);
        })();
    </script>
    @endif
</x-top-layout>
