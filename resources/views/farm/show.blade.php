<x-top-layout>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <style>
        .swiper-button-next, .swiper-button-prev {
            color: #fff; 
            text-shadow: 0 1px 3px rgba(0,0,0,0.5);
        }
        .swiper-pagination-bullet-active {
            background: #fff;
        }
        .responsive-catchcopy {
            font-size: 1.5rem; /* Mobile default */
            line-height: 1.4;
        }
        @media (min-width: 768px) {
            .responsive-catchcopy {
                font-size: 2.5rem; /* Tablet/Desktop */
            }
        }
        /* Scroll Animation */
        .animate-on-scroll {
            opacity: 0 !important;
            transform: translateY(20px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .animate-on-scroll.is-visible {
            opacity: 1 !important;
            transform: translateY(0);
        }
        /* Custom Hover Scale */
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
            z-index: 10;
        }
    </style>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <!-- パンくずリストの表示 -->
    <nav aria-label="breadcrumb" class="mt-10">
        {!! Breadcrumbs::render('farm.show', $farm) !!}
    </nav>

    <div class="mt-20 mb-5 flex items-center justify-center">
            @if ($farm->vr)
                {{-- VR画像がある場合：A-Frameで360度表示 --}}
                <div class="w-full h-[50vh] relative z-0"> {{-- z-0を追加してメニュー等が隠れるのを防ぎます --}}
                    <a-scene embedded class="w-full h-full">
                        {{-- URLをそのままsrcに指定 --}}
                        <a-sky src="{{ $farm->vr }}" rotation="0 -90 0"></a-sky>
                    </a-scene>
                </div>
                
            @elseif ($farm->farmImages->isNotEmpty())
                <div class="swiper mySwiper w-full relative group">
                    <div class="swiper-wrapper">
                        @foreach ($farm->farmImages as $image)
                            <div class="swiper-slide w-full h-full">
                                <img src="{{ $image->image_path }}" alt="{{ $farm->farm_name }}" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next !w-12 !h-12 !bg-black/20 hover:!bg-black/40 rounded-full backdrop-blur-sm transition-all after:!text-xl"></div>
                    <div class="swiper-button-prev !w-12 !h-12 !bg-black/20 hover:!bg-black/40 rounded-full backdrop-blur-sm transition-all after:!text-xl"></div>
                    <div class="swiper-pagination"></div>
                </div>
                
            @else
                {{-- 画像が何もない場合 --}}
                <div class="w-full h-[50vh] bg-gray-200 flex items-center justify-center">
                    <p class="text-gray-500 text-xl font-bold">No Image Available</p>
                </div>
            @endif
        </div>
    

    <div class="flex items-center justify-center">
        <p>{{ $farm->theme }}</p>
    </div>

    <div class="flex items-center justify-center mt-6 mb-4 px-4">
        <h2 class="responsive-catchcopy font-bold text-stone-800">{{ $farm->catchcopy }}</h2>
    </div>
    <h2 class="heading06" data-en="{{ $farm->prefecture }}">{{ $farm->farm_name }}</h2>

    @if($farm->animals->isNotEmpty())
        <div class="story">
            <p class="mt-20 text-[#e0db85]">FEATURES</p>
        </div>
        <div class="note-title">
            <p>牧場の特徴</p>
        </div>

        <div class="container mx-auto px-4 mb-20 max-w-6xl animal-section">
            @foreach($farm->animals->sortBy('created_at') as $animal)
                {{-- $loop->iteration が奇数なら「左から」、偶数なら「右から」 --}}
                @php
                    $isEven = $loop->iteration % 2 === 0;
                    $animClass = $isEven ? 'slide-in-right' : 'slide-in-left';
                    $rowClass = $isEven ? 'reverse' : '';
                @endphp

                <div class="magazine-row {{ $rowClass }} {{ $animClass }}">
                    {{-- 画像エリア --}}
                    <div class="w-full md:w-1/2 flex justify-center">
                        @if($animal->animal_image)
                            @if($animal->is_vr)
                                {{-- 360°画像の場合 --}}
                                {{-- magazine-imageのサイズに合わせたラッパー --}}
                                <div class="w-full max-w-[500px] h-[350px] rounded-lg overflow-hidden shadow-xl relative z-0">
                                    <a-scene embedded class="w-full h-full">
                                        <a-sky src="{{ $animal->animal_image }}" rotation="0 -90 0"></a-sky>
                                    </a-scene>
                                </div>
                            @else
                                {{-- 普通の画像の場合 --}}
                                <img src="{{ $animal->animal_image }}" alt="{{ $animal->animal_name }}" class="magazine-image">
                            @endif                        @else
                            <div class="magazine-image bg-gray-200 flex items-center justify-center text-gray-500 font-bold text-xl">
                                No Image
                            </div>
                        @endif
                    </div>

                    {{-- テキストエリア --}}
                    <div class="magazine-content">
                        <h3 class="magazine-title">{{ $animal->animal_name }}</h3>
                        <p class="magazine-desc">{{ $animal->animal_info }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
    <div class="story">
        <p class="mt-20 text-[#e0db85]">GALLERY</p>
    </div>
    <div class="note-title">
        <p>撮影一覧</p>
    </div>
    <div class="container mx-auto px-4 mb-20 max-w-5xl">
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
            @foreach ($farm->products as $key => $product)
                <div class="group relative aspect-square bg-stone-100 rounded-xl overflow-hidden cursor-pointer shadow-md hover:shadow-xl hover-scale animate-on-scroll"
                     style="transition-delay: {{ $key * 100 }}ms;"
                     onclick="openModal({{ json_encode([
                        'image' => $product->product_image,
                        'name' => $product->product_name,
                        'info' => nl2br(e($product->product_info))
                    ]) }})">
                    <img src="{{ $product->product_image }}" alt="{{ $product->product_name }}" 
                         class="w-full h-full object-cover">
                    
                    <!-- Overlay with Name -->
                    <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/70 to-transparent p-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center h-1/2">
                        <p class="text-white font-bold text-sm tracking-wide text-center drop-shadow-md">{{ $product->product_name }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="container mx-auto my-10">
        
        <div class="story">
            <p class="mt-20 text-[#e0db85]">INFO
            </p>
        </div>
        <div class="note-title">
        <p>牧場情報</p>
        </div>
        <table class="table-auto border-collapse w-full bg-white rounded-lg shadow-md">
            <tbody>
                <tr>
                    <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">牧場名</th>
                    <td class="px-4 py-2">{{ $farm->farm_name }}</td>
                </tr>
                <tr>
                    <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">住所</th>
                    <td class="px-4 py-2">{{ $farm->prefecture }} {{ $farm->address }}</td>
                </tr>
                <tr>
                    <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">主な動物</th>
                    <td class="px-4 py-2">
                        @foreach ($farm->kinds as $kind)
                            <span class="inline-block bg-green-100 text-green-800 text-sm font-semibold px-2 py-1 rounded">{{ $kind->kind }}</span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">こだわり</th>
                    <td class="px-4 py-2">
                        @foreach ($farm->keywords as $keyword)
                            <span class="text-xs font-semibold px-2 py-1 rounded">#{{ $keyword->keyword }}</span>
                        @endforeach
                    </td>
                </tr>
                <tr>
                    <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">体験可否</th>
                    <td class="px-4 py-2">
                        @if ($farm->has_experience)
                            <span class="text-green-600 font-semibold">体験・視察可能</span>
                        @else
                            <span class="text-gray-500 font-semibold">ー</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">HP</th>
                    <td class="px-4 py-2">
                        @if ($farm->hp_link)
                            <a href="{{ $farm->hp_link }}" target="_blank" class="text-blue-500 hover:underline">{{ $farm->hp_link }}</a>
                        @else
                            <span class="text-gray-500">ー</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">Instagram</th>
                    <td class="px-4 py-2">
                        @if ($farm->instagram_link)
                        <a href="{{ $farm->instagram_link }}" target="_blank">
                            <img src="{{ asset('storage/Instagram.png') }}" alt="Instagram Icon" class="w-6 h-6 inline-block" />
                        </a>
                        @else
                            <span class="text-gray-500">ー</span>
                        @endif
                    </td>
                </tr>
                
            </tbody>
        </table>
    </div>

    
<!-- モーダルウィンドウ -->
<div id="modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 items-center justify-center z-50">
    <div class="modal-content bg-white w-3/4 md:w-1/2 p-5 rounded-lg relative">
        <!-- 閉じるボタン -->
        <button onclick="closeModal()" class="close absolute top-2 right-2 text-gray-500 hover:text-gray-700 text-xl">×</button>

        <!-- 画像を表示するエリア -->
        <div id="modal-image" class="mb-4">
            <img src="" alt="Product Image" class="w-full h-auto max-h-60 object-contain mx-auto">
        </div>

        <!-- タイトル -->
        <h2 id="modal-title" class="text-xl font-bold mb-4"></h2>

        <!-- 説明 -->
        <div id="modal-info" class="text-gray-700"></div>
    </div>
</div>

    <!-- farm_idに一致する記事の内容を表示 -->
    <div class="story">
        <p class="mt-20 text-[#e0db85]">NOTE</p>
    </div>
    <div class="note-title">
        <p>訪問記・取材</p>
    </div>
    <div class="note-wrap">
        <div class="note-wrap-in">
            @foreach ($articles as $article)
            <div class="note-item">
                <a href="{{ route('article.show', $article->id) }}"> <!-- モーダルではなくリンク -->
                    <div class="pic">
                        <img src="{{ json_decode($article->article_images)[0] }}" alt="{{ $article->title }}">
                    </div>
                    <p>{{ $article->title }}</p>
                    <span class="more-link">もっとみる →</span>
                </a>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        // Swiper設定（ここは変更なし）
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 0,
            centeredSlides: true,
            speed: 1000,
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
            },
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            effect: 'fade', 
            fadeEffect: {
                crossFade: true
            },
        });

        // ▼▼▼ ここを修正しました（何度でも動くバージョン） ▼▼▼
        document.addEventListener('DOMContentLoaded', function() {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // 画面に入った時：クラスを追加してアニメーション開始
                        entry.target.classList.add('is-visible');
                    } else {
                        // 画面から出た時：クラスを削除してリセット（これで次回も動きます）
                        entry.target.classList.remove('is-visible');
                    }
                });
            }, {
                threshold: 0.1, // 10%見えたら発火
                rootMargin: "0px 0px -50px 0px" // 少し余裕を持たせる
            });

            // 監視対象1: 既存のアニメーション要素
            const elements = document.querySelectorAll('.animate-on-scroll');
            elements.forEach((el) => {
                observer.observe(el);
            });

            // 監視対象2: 雑誌風レイアウトの動物リスト
            const magazineRows = document.querySelectorAll('.magazine-row');
            magazineRows.forEach((el) => {
                observer.observe(el);
            });
        });
    </script>

    <script>
        // モーダル用JS（変更なし）
        function openModal(data) {
            const modal = document.getElementById("modal");
            const modalImage = document.getElementById("modal-image").querySelector("img");
            const modalTitle = document.getElementById("modal-title");
            const modalInfo = document.getElementById("modal-info");

            modalImage.src = data.image || "{{ asset('storage/noimage.jpg') }}";
            modalImage.alt = data.name || "Product Image";
            modalTitle.textContent = data.name;
            modalInfo.innerHTML = data.info;

            modal.classList.remove("hidden");
            modal.classList.add("flex");
        }

        function closeModal() {
            const modal = document.getElementById("modal");
            modal.classList.add("hidden");
            modal.classList.remove("flex");
        }
    </script>      
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