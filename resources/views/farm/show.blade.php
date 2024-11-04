<x-top-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <!-- パンくずリストの表示 -->
    <nav aria-label="breadcrumb" class="mt-10">
        {!! Breadcrumbs::render('farm.show', $farm) !!}
    </nav>

    <div class="mt-20 mb-5 flex items-center justify-center">
        {!! $farm->vr !!}
    </div>

    <div class="flex items-center justify-center">
        <p>{{ $farm->theme }}</p>
    </div>

    <div class="flex items-center justify-center">
        <h2>{{ $farm->catchcopy }}</h2>
    </div>
    <h2 class="heading06" data-en="{{ $farm->prefecture }}">{{ $farm->farm_name }}</h2>

    <div class="flex items-center justify-center">
        <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
            <div class="cp_box">
                <input id="cp01" type="checkbox">
                <label for="cp01"></label>
                <div class="cp_container">
                    <p>{!! $farm->farm_info !!}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- farm_idに一致する記事の内容を表示 -->
    <div class="story">
        <p class="mt-20 text-[#e0db85]">NOTE</p>
    </div>
    <div class="note-title">
    <p>〜訪問記録・インタビュー〜</p>
    </div>
    <div class="noteshow-wrap">
        <div class="noteshow-wrap-in">
        @foreach ($articles as $article)
            <div class="note-item">
                <a href="{{ route('article.show', $article->id) }}">
                    <div class="pic">
                        <img src="{{ json_decode($article->article_images)[0] }}" alt="{{ $article->title }}">
                    </div>
                    <p>{{ $article->title }}</p>
                    <a href="{{ route('article.show', $article->id) }}" class="more-link">もっとみる →</a>
                </a>
            </div>
        @endforeach
        </div>
    </div>


</x-top-layout>


    {{-- <a href="{{ route('farm.community', ['farm' => $farm->id]) }}">
        <button class="ml-10 mb-2 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
            コミュニティへ行こう！  
        </button>
    </a>
    <a href="{{ route('qrs.index', ['farm' => $farm->id]) }}"> --}}
    {{-- <a href="{{ route('user.community.index', ['farm' => $farm->id]) }}"> --}}
        {{-- <button class="ml-10 mb-2 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
            飲食関係の方向け  
        </button>
    </a> --}}
       {{-- <div class="flex items-center justify-center">
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
    </div> --}}

    {{-- <div class="bg my-20 pb-10 px-10">
        <div class="sub-title mt-10 mb-5 py-5">ー商品 ー</div>
        <div class="swiper mx-5"> --}}
            <!-- 必要に応じたwrapper -->
            {{-- <div class="swiper-wrapper"> --}}
            <!-- スライド -->
            {{-- @foreach ($farm->products as $product)
            <div class="swiper-slide">
                <img src="{{ $product->product_image }}" alt="">
                <div class="slide-content">
                    <div class="slide-name">{{ $product->product_name }}</div>
                    <p class="slide-info mx-4">{!! nl2br(e($product->product_info)) !!}</p>
                </div>
            </div>
            @endforeach
            </div> --}}
            <!-- 必要に応じてページネーション -->
            {{-- <div class="swiper-pagination"></div> --}}
            <!-- 必要に応じてナビボタン -->
            {{-- <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>
        </div>   --}}
{{-- 
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
        </div>   --}}