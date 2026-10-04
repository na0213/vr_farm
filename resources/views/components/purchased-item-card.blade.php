{{-- 購入した商品のカード。showFarm を付けると牧場名(牧場ページへのリンク)も出す。
     写真が2枚以上あれば、下の小さい写真を押すと大きい写真が切り替わる(Alpine) --}}
@props(['item', 'showFarm' => false])
@php($images = $item->images())

<article class="soft-card flex flex-col"
         @if (count($images) > 1) x-data="{ shown: 0, images: @js($images) }" @endif>
    <img src="{{ $images[0] }}" alt="{{ $item->item_name }}" loading="lazy" class="w-full aspect-square object-cover"
         @if (count($images) > 1) x-bind:src="images[shown]" @endif>
    <div class="p-5 flex flex-col flex-1">
        @if (count($images) > 1)
            <div class="item-thumbs" role="group" aria-label="{{ $item->item_name }}の写真">
                @foreach ($images as $i => $image)
                    <button type="button" class="item-thumb" aria-label="{{ $i + 1 }}枚目を見る"
                            aria-pressed="{{ $i === 0 ? 'true' : 'false' }}"
                            x-on:click="shown = {{ $i }}"
                            x-bind:aria-pressed="(shown === {{ $i }}).toString()">
                        <img src="{{ $image }}" alt="" loading="lazy">
                    </button>
                @endforeach
            </div>
        @endif
        <h3 class="font-bold text-stone-800 leading-snug">{{ $item->item_name }}</h3>
        @if ($showFarm && $item->farm)
            <a href="{{ route('farm.show', $item->farm->id) }}" class="text-sm text-stone-500 mt-1 hover:underline">
                {{ $item->farm->prefecture }}・{{ $item->farm->farm_name }}
            </a>
        @endif
        @if ($item->item_comment)
            <p class="text-sm text-stone-600 mt-3 leading-relaxed">{!! nl2br(e($item->item_comment)) !!}</p>
        @endif
        @if ($item->item_link)
            <div class="item-link mt-auto pt-4">
                <a href="{{ $item->item_link }}" target="_blank" rel="noopener" class="btn-butter">販売ページを見る</a>
            </div>
        @endif
    </div>
</article>
