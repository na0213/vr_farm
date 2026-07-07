<x-top-layout>
    <x-slot name="title">お取り寄せ</x-slot>
    <x-slot name="metaDescription">動物がのびのび育つ牧場の卵・お肉・乳製品。作り手の顔が見える、お取り寄せできる商品を紹介します。</x-slot>

    <div class="container mx-auto px-4 py-12 max-w-5xl">
        <div class="note-title">
            <p class="wavy-underline">お取り寄せ</p>
        </div>
        <p class="text-center text-stone-600 mt-6 mb-10 leading-relaxed">
            動物がのびのび育つ牧場の、おいしいもの。<br>
            気になる商品は、各牧場の販売ページからお取り寄せできます。
        </p>

        @if ($products->isEmpty())
            <div class="text-center py-16">
                <p class="text-stone-500 mb-6">商品情報はただいま準備中です。もうしばらくお待ちください。</p>
                <a href="{{ route('farm.index') }}" class="inline-block text-white bg-yellow-500 hover:bg-yellow-600 rounded px-8 py-3">牧場を探してみる</a>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach ($products as $product)
                    <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col">
                        @if ($product->product_image)
                            <img src="{{ $product->product_image }}" alt="{{ $product->product_name }}" loading="lazy" class="w-full h-48 object-cover">
                        @endif
                        <div class="p-4 flex flex-col flex-1">
                            <h2 class="font-bold text-stone-800">{{ $product->product_name }}</h2>
                            @if ($product->farm)
                                <a href="{{ route('farm.show', $product->farm->id) }}" class="text-sm text-stone-500 mt-1 hover:underline">
                                    {{ $product->farm->prefecture }}・{{ $product->farm->farm_name }}
                                </a>
                            @endif
                            @if ($product->product_info)
                                <p class="text-sm text-stone-600 mt-2 flex-1">{{ Str::limit($product->product_info, 60) }}</p>
                            @endif
                            <a href="{{ $product->product_link }}" target="_blank" rel="noopener"
                               class="mt-4 inline-block text-center text-white bg-yellow-500 hover:bg-yellow-600 rounded px-4 py-2">
                                販売ページを見る
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="text-center mt-14 text-sm text-stone-500 leading-relaxed">
            掲載している商品は、各牧場の公式販売ページ等を紹介するものです。<br>
            価格・在庫は販売ページでご確認ください。
        </div>
    </div>
</x-top-layout>
