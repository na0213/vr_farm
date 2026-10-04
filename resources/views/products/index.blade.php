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
            @foreach ($farms as $farm)
                <section class="mb-16" aria-labelledby="farm-{{ $farm->id }}">
                    <h2 id="farm-{{ $farm->id }}" class="mb-6 flex flex-wrap items-baseline gap-x-3 border-b border-stone-200 pb-3">
                        <span class="text-xl font-bold text-stone-800">{{ $farm->farm_name }}</span>
                        <span class="text-sm text-stone-500">{{ $farm->prefecture }}</span>
                    </h2>
                    <div class="grid product-grid grid-stagger">
                        @foreach ($farm->purchasedItems as $item)
                            <x-purchased-item-card :item="$item" />
                        @endforeach
                    </div>
                    <p class="mt-6 text-right">
                        <a href="{{ route('farm.show', $farm->id) }}" class="btn-link text-sm hover:underline">{{ $farm->farm_name }}のページへ →</a>
                    </p>
                </section>
            @endforeach
        @endif

        <div class="text-center mt-14 text-sm text-stone-500 leading-relaxed">
            写真と感想は、運営者が自分で購入したときのものです。<br>
            価格・在庫は販売ページでご確認ください。
        </div>
    </div>
</x-top-layout>
