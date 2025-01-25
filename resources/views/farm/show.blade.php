<x-top-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <!-- パンくずリストの表示 -->
    <nav aria-label="breadcrumb" class="mt-10">
        {!! Breadcrumbs::render('farm.show', $farm) !!}
    </nav>

    <div class="mt-20 mb-5 flex items-center justify-center">
        @if ($farm->vr)
            {!! $farm->vr !!}
        @elseif ($farm->farmImages->isNotEmpty())
            <div class="imageshow-container">
                @foreach ($farm->farmImages as $image)
                    <div class="image">
                        <img src="{{ $image->image_path }}" alt="{{ $farm->farm_name }}" class="image-image">
                    </div>
                @endforeach
    
                <!-- 左右の矢印ボタン -->
                <a class="prev" onclick="changeSlide(-1)">&#10094;</a>
                <a class="next" onclick="changeSlide(1)">&#10095;</a>
            </div>
        @else
            <p>No Image</p>
        @endif
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

    <div class="container mx-auto my-10">
        <div class="story">
            <p class="mt-20 text-[#e0db85]">INFO
            </p>
        </div>
        <div class="note-title">
        <p>〜牧場情報〜</p>
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
                            <span class="inline-block bg-blue-100 text-blue-800 text-sm font-semibold px-2 py-1 rounded">{{ $keyword->keyword }}</span>
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
    <div class="story">
        <p class="mt-20 text-[#e0db85]">PROEUCTS</p>
    </div>
    <div class="note-title">
        <p>〜主な商品〜</p>
    </div>
    <div class="products-wrap note-wrap">
        <div class="note-wrap-in">
            @foreach ($farm->products as $product)
            <div class="note-item">
                <div class="pic" onclick="openModal({{ json_encode([
                    'name' => $product->product_name,
                    'info' => nl2br(e($product->product_info))
                ]) }})">
                    <img src="{{ $product->product_image }}" alt="{{ $product->product_name }}">
                </div>
            </div>
            @endforeach
        </div>
    </div>
    
    <!-- モーダルウィンドウ -->
    <div id="modal" class="hidden fixed top-0 left-0 w-full h-full bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white w-3/4 md:w-1/2 p-5 rounded-lg relative">
            <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">×</button>
            <h2 id="modal-title" class="text-xl font-bold mb-4"></h2>
            <div id="modal-info" class="text-gray-700"></div>
        </div>
    </div>
    

    <!-- farm_idに一致する記事の内容を表示 -->
    <div class="story">
        <p class="mt-20 text-[#e0db85]">NOTE</p>
    </div>
    <div class="note-title">
        <p>〜訪問記録・インタビュー〜</p>
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

    <script>
        let slideIndex = 0;
        showSlides(slideIndex);
    
        // スライドを切り替える関数
        function showSlides(n) {
            let slides = document.getElementsByClassName("image");
    
            // すべてのスライドを非表示にする
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
    
            // インデックスを正しい範囲内に収める
            slideIndex = (n + slides.length) % slides.length;
    
            // 現在のスライドを表示
            slides[slideIndex].style.display = "block";
        }
    
        // 左右の矢印でスライドを切り替える
        function changeSlide(n) {
            showSlides(slideIndex + n);
        }
    
        // 自動スライド (4秒ごとに次のスライド)
        setInterval(function() {
            changeSlide(1);
        }, 4000);
    </script>
    <script>
        function openModal(data) {
            const modal = document.getElementById('modal');

            // タイトルと情報を設定
            document.getElementById('modal-title').textContent = data.name;
            document.getElementById('modal-info').innerHTML = data.info;

            // モーダルを表示
            modal.classList.remove('hidden');
        }

        function closeModal() {
            const modal = document.getElementById('modal');
            modal.classList.add('hidden');
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