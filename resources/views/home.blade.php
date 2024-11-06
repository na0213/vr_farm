<x-top-layout>
  <!-- Session Status -->
  <x-auth-session-status class="mb-4" :status="session('status')" />

  <div class="slide-title">牧場から<br>食を知る</div>
  <div class="swiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide">
        <div class="swiper-img-wrapper">
          <div class="swiper-img">
            <img
              src="{{asset('storage/top1.jpg')}}"
              alt=""
            />
          </div>
        </div>
      </div>
      <div class="swiper-slide">
        <div class="swiper-img-wrapper">
          <div class="swiper-img">
            <img
              src="{{asset('storage/top2.jpg')}}"
              alt=""
            />
          </div>
        </div>
      </div>
      <div class="swiper-slide">
        <div class="swiper-img-wrapper">
          <div class="swiper-img">
            <img
              src="{{asset('storage/top3.jpg')}}"
              alt=""
            />
          </div>
        </div>
      </div>
    </div>
    <!-- ページネーション -->
    <div class="swiper-pagination"></div>
  </div>

    {{-- <a href="{{ route('farm.index') }}">
        <div class="fuwafuwa"> --}}
            {{-- <div class="farm_link">牧場へ行く</div><br><br><br> --}}
            {{-- <p class="farm_link">WELCOME</p> --}}
        {{-- </div>
    </a> --}}

  <div class="wave">
    <div class="story">
      <p class="text-[#df8e8f]">STORY</p>
      <p class="con_text">
        「アニマルウェルフェア」<br>
        「放牧」<br>
        「循環型」<br>
        人・動物・環境が循環する牧場<br><br>
        牧場ごとの味をたんのうしたい<br>
        牧場のある風景へ足を運びたい<br>
        <br>
        そこから始まった<br>
        わたしの牧場旅<br>
        訪れた牧場をどんどん掲載していきます<br>
        <br>
      </p>
    </div>
  </div>

  <div class="story">
    <p class="mt-10 text-[#e09885]">FARM</p>
  </div>
  <div class="note-title">
    <p>〜訪れた牧場〜</p>
  </div>
  <div class="circle">
    <a href="{{ route('farm.index') }}">
    <div class="concept bg-image-1">
        <p class="center-text">
            推し牧場を<br>みつけよう！
        </p>
    </div>
    </a>
    {{-- <a href="{{ route('farm.index') }}">
      <div class="concept bg-image-2">
          <p class="center-text">
              牧場を<br>たべよう！
          </p>
      </div>
    </a> --}}
  </div>

  <div class="story">
    <p class="mt-20 text-[#e0db85]">NOTE</p>
  </div>
  <div class="note-title">
    <p>〜訪問記録・インタビュー〜</p>
</div>
<div class="note-wrap">
    <div class="note-wrap-in">
        @foreach ($articles as $article)
            @if($article->is_published)
                <div class="note-item">
                    <a href="{{ route('article.show', $article->id) }}">
                        <div class="pic"><img src="{{ json_decode($article->article_images)[0] }}" alt="{{ $article->title }}"></div>
                        <p>{{ $article->title }}</p>
                        <a href="{{ route('article.show', $article->id) }}" class="more-link">もっとみる →</a>
                    </a>
                </div>
            @endif
        @endforeach
    </div>
</div>
<script>
const cardWrap = document.querySelector('.card-wrap');
const cardWrapIn = document.querySelector('.card-wrap-in');

// スクロールが開始された時にアニメーションを一時停止
cardWrap.addEventListener('scroll', () => {
    cardWrapIn.style.animationPlayState = 'paused';

    // 一定時間後にアニメーションを再開
    clearTimeout(cardWrap.scrollTimeout);
    cardWrap.scrollTimeout = setTimeout(() => {
        cardWrapIn.style.animationPlayState = 'running';
    }, 3000); // 3秒後にアニメーションを再開
});
</script>
</x-top-layout>

