{{-- <x-farm-layout> --}}
<x-appshow-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="top-title">
        <h1 class="top-content">
          <span class="farm-name">{{ $farm->farm_name }}</span>
          <span class="farm-address">{{ $farm->prefecture }}</span>
        </h1>
    </div>

    <div class="mt-20 mb-5 flex items-center justify-center">
        {!! $farm->vr !!}
    </div>

    <div class="flex items-center justify-center">
        <p>{{ $farm->theme }}</p>
    </div>

    <div class="flex items-center justify-center">
        {{-- <div class="flex items-center justify-center"> --}}
            <h2>{{ $farm->catchcopy }}</h2>
        {{-- </div> --}}
        <div id="favorite-icon-{{ $farm->id }}" class="ml-10">
            @if(Auth::user() && $farm->isFavoriteBy(Auth::user()))
                <i class="fas fa-heart text-red-500"></i>
            @else
                <i class="far fa-heart text-red-500"></i>
            @endif
            <span>{{ $farm->likes->count() }}</span>
        </div>
    </div>

    <a href="{{ route('user.community.index', ['farm' => $farm->id]) }}">
    <button class="ml-10 mb-2 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
        コミュニティへ行こう！  
    </button>
    </a>
    <div class="flex items-center justify-center">
        <div class="w-4/5 mt-15 p-5 rounded overflow-hidden shadow-lg">
            <div class="sub-title">ー Story ー</div>
            <div class="cp_box">
                <input id="cp01" type="checkbox">
                <label for="cp01"></label>
                <div class="cp_container">
                    <p>{!! $farm->farm_info !!}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="flex items-center justify-center">
        <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
            <div class="sub-title">ー飼育 ー</div>
            <div class="cp_box">
                <input id="cp02" type="checkbox">
                <label for="cp02"></label>
                <div class="cp_container">
                    <p class="mb-4">何を食べて育っているの？<br>どんな環境で育っているの？</p>
                    @foreach ($farm->animals as $animal)
                    <div class="card-bg flex flex-col rounded-lg mb-2 text-surface shadow-secondary-1 dark:bg-surface-dark dark:text-white md:flex-row">                        <img
                          class="h-96 w-full rounded-t-lg object-cover md:h-auto md:w-64 md:!rounded-none md:!rounded-s-lg"
                          src="{{ $animal->animal_image }}"
                          alt="" />
                        <div class="flex flex-col justify-start p-6">
                          <h5 class="mb-2 text-xl font-medium">{{ $animal->animal_name }}</h5>
                          <p class="mb-4 text-base">{{ $animal->animal_info }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="bg my-20 pb-10 px-10">
        <div class="sub-title mt-10 mb-5 py-5">ー商品 ー</div>
        <div class="swiper mx-5">
            <!-- 必要に応じたwrapper -->
            <div class="swiper-wrapper">
            <!-- スライド -->
            @foreach ($farm->products as $product)
            <div class="swiper-slide">
                <img src="{{ $product->product_image }}" alt="">
                <div class="slide-content">
                    <div class="slide-name">{{ $product->product_name }}</div>
                    <p class="slide-info mx-4">{!! nl2br(e($product->product_info)) !!}</p>
                </div>
            </div>
            @endforeach
            </div>
            <!-- 必要に応じてページネーション -->
            <div class="swiper-pagination"></div>
            <!-- 必要に応じてナビボタン -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>  

        <div class="sub-title mt-10 mb-5 py-5">ー取扱い店 ー</div>
        <div class="relative overflow-x-auto">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-900 uppercase dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            取扱い店
                        </th>
                        <th scope="col" class="px-6 py-3">
                            所在地
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($farm->stores as $store)
                    <tr class="bg-white dark:bg-gray-800">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                         @if (!empty($store->store_link))
                            <a href="{{ $store->store_link }}" target="_blank">{{ $store->store_name }}</a>
                        @else
                            {{ $store->store_name }}
                        @endif
                        </th>
                        <td class="px-6 py-4">
                            {{ $store->store_address }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>  
    </div>
<script>
    document.querySelectorAll('[id^="favorite-icon-"]').forEach(element => {
    element.addEventListener('click', async () => {
        const farmId = element.getAttribute('id').split('-')[2];
        const response = await fetch(`/farms/${farmId}/favorite`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
        });

        if (response.ok) {
            const { isFavorite, favoritesCount } = await response.json();
            const icon = element.querySelector('i');
            const countSpan = element.querySelector('span');
            icon.classList.remove('fas', 'far');
            icon.classList.add(isFavorite ? 'fas' : 'far');
            countSpan.textContent = favoritesCount;
        }
    });
});
</script>

</x-appshow-layout>