<x-app-layout>
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
        <div id="favorite-icon-{{ $farm->id }}" class="ml-10">
            @if(Auth::user() && $farm->isFavoriteBy(Auth::user()))
                <i class="fas fa-heart text-red-500"></i>
            @else
                <i class="far fa-heart text-red-500"></i>
            @endif
            <span>{{ $farm->likes->count() }}</span>
        </div>
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
      {{-- <a href="{{ route('user.community.index', ['farm' => $farm->id]) }}">
        <button class="ml-10 mb-2 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
            コミュニティへ行こう！  
        </button>
        </a> --}}
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

</x-app-layout>