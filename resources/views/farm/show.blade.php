<x-top-layout>
    <x-slot name="title">{{ $farm->farm_name }}({{ $farm->prefecture }})</x-slot>
    <x-slot name="metaDescription">{{ $farm->catchcopy ? $farm->catchcopy . ' — ' : '' }}{{ $farm->prefecture }}の{{ $farm->farm_name }}の紹介ページ。飼い方のこだわり、商品、お取り寄せ情報を掲載しています。</x-slot>
    <x-slot name="jsonLd">{!! \App\Services\StructuredData::json(\App\Services\StructuredData::farm($farm)) !!}</x-slot>
    @if ($farm->farmImages->isNotEmpty())
        <x-slot name="ogImage">{{ $farm->farmImages->first()->image_path }}</x-slot>
    @endif
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
            {{-- Pannellum用のコンテナ --}}
            <div id="panorama-main" class="w-full h-[50vh] relative z-0"></div>
            {{-- 個別の設定スクリプト --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        pannellum.viewer('panorama-main', {
                            "type": "equirectangular",
                            "panorama": "{{ $farm->vr }}", // 画像のURL
                            "autoLoad": true,              // ページ読み込みと同時に表示（ここが魅力維持のポイント）
                            "autoRotate": -2,              // 左へゆっくり自動回転（動きが出るのでリッチに見えます）
                            "compass": false,
                            "showControls": false          // コントローラーを隠してスッキリさせる場合（お好みでtrueに）
                        });
                    });
                </script>
                
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
                                <div id="panorama-animal-{{ $animal->id }}" class="w-full max-w-[500px] h-[350px] rounded-lg overflow-hidden shadow-xl relative z-0"></div>
                                
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        pannellum.viewer('panorama-animal-{{ $animal->id }}', {
                                            "type": "equirectangular",
                                            "panorama": "{{ $animal->animal_image }}",
                                            "autoLoad": true,  // ★重要：リスト内の画像は「画面に入ったら読み込む」等の制御がないと重くなるため、最初は読み込まない設定が良いかもしれません
                                            // もしすべて自動ロードしたい場合は true にしますが、数が多いと重くなります
                                            "autoRotate": -2
                                        });
                                    });
                                </script>
                            @else
                                <img src="{{ $animal->animal_image }}" alt="{{ $animal->animal_name }}" class="magazine-image">
                            @endif
                        @else
                            {{-- No Image --}}
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
        <div class="grid gallery-grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
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
                {{-- キーワードが1つも付いていない牧場では、空の欄を出さない --}}
                @if ($farm->keywords->isNotEmpty())
                    <tr>
                        <th class="px-4 py-2 bg-gray-200 text-left font-medium text-gray-600">こだわり</th>
                        <td class="px-4 py-2">
                            @foreach ($farm->keywords as $keyword)
                                <span class="text-xs font-semibold px-2 py-1 rounded">#{{ $keyword->keyword }}</span>
                            @endforeach
                        </td>
                    </tr>
                @endif
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
<div id="modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-70 items-center justify-center z-50 transition-opacity duration-300">
    {{-- ↓ 幅を広げました（w-11/12 md:w-3/4 max-w-6xl） --}}
    <div class="modal-content bg-white w-11/12 md:w-3/4 max-w-6xl p-6 rounded-xl relative shadow-2xl flex flex-col max-h-[90vh]">
        <button onclick="closeModal()" class="close absolute top-3 right-4 text-gray-500 hover:text-gray-800 text-3xl font-light focus:outline-none">&times;</button>

        <div id="modal-image" class="mb-4 flex-1 flex items-center justify-center overflow-hidden">
            {{-- ↓ 画像の高さを大きくしました（max-h-[75vh]） --}}
            <img src="" alt="Product Image" class="w-full h-auto max-h-[75vh] object-contain mx-auto rounded">
        </div>

        <h2 id="modal-title" class="text-2xl font-bold mb-2 text-center text-gray-800"></h2>

        <div id="modal-info" class="text-gray-600 text-center overflow-y-auto max-h-32"></div>
    </div>
</div>

    @if ($farm->purchasedItems->isNotEmpty())
        <div id="products" class="story">
            <p class="mt-20 text-[#e0db85]">PRODUCTS</p>
        </div>
        <div class="note-title">
            <p>買ってみた</p>
        </div>
        <div class="container mx-auto px-4 mb-10 max-w-5xl">
            <p class="text-center text-sm text-stone-500 mb-6">運営者が自分で買って、撮影したものです。</p>
            {{-- 横にスライド(スマホは指で、パソコンは左右のボタンでも動かせる) --}}
            <div class="product-slider" data-slider>
                <ul class="product-slider-track" tabindex="0" aria-label="買ってみた商品(横にスクロールできます)">
                    @foreach ($farm->purchasedItems as $item)
                        <li class="product-slide"><x-purchased-item-card :item="$item" /></li>
                    @endforeach
                </ul>
                <button type="button" class="product-slider-btn is-prev" data-dir="-1" aria-label="前の商品へ" hidden>&lsaquo;</button>
                <button type="button" class="product-slider-btn is-next" data-dir="1" aria-label="次の商品へ" hidden>&rsaquo;</button>
            </div>
        </div>
    @endif

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
        // 購入した商品の横スライド: 左右のボタンで1枚ずつ動かす。端ではボタンを隠す
        document.querySelectorAll('[data-slider]').forEach(function (slider) {
            const track = slider.querySelector('.product-slider-track');
            const buttons = slider.querySelectorAll('.product-slider-btn');
            const smooth = !window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            function update() {
                const max = track.scrollWidth - track.clientWidth;
                buttons[0].hidden = track.scrollLeft <= 8;
                buttons[1].hidden = track.scrollLeft >= max - 8;
            }

            buttons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const slide = track.querySelector('.product-slide');
                    const step = slide.getBoundingClientRect().width + parseFloat(getComputedStyle(track).columnGap || 0);
                    track.scrollBy({ left: step * Number(button.dataset.dir), behavior: smooth ? 'smooth' : 'auto' });
                });
            });
            track.addEventListener('scroll', update, { passive: true });
            window.addEventListener('resize', update);
            update();
        });
    </script>

    <script>
        // 撮影一覧のモーダル
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

        // 暗い背景を押す/Esc でも閉じる
        document.getElementById("modal").addEventListener("click", function (event) {
            if (event.target === this) closeModal();
        });
        document.addEventListener("keydown", function (event) {
            if (event.key === "Escape") closeModal();
        });
    </script>      
</x-top-layout>

            <!-- 必要に応じたwrapper -->
            <!-- スライド -->
            <!-- 必要に応じてページネーション -->
            <!-- 必要に応じてナビボタン -->
