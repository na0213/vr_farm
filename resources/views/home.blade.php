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
        人・動物・環境が循環する牧場を巡る<br><br>
        牧場ごとの味をたんのうし<br>
        牧場のある風景へ足を運ぶ<br>
        <br>
        そこから始まった<br>
        わたしの牧場旅<br>
        「牧場から食を知ろう」<br>
        <br>
      </p>
    </div>
  </div>

  <div class="story">
    <p class="mt-10 text-[#e09885]">FARM</p>
  </div>
  <div class="note-title">
    <p>〜牧場検索〜</p>
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
    <!-- 以下で記事リストをもう1回繰り返す -->
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

{{-- <div class="note-wrap">
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
</div> --}}
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
<script>
  document.addEventListener('DOMContentLoaded', () => {
  const noteWrapIn = document.querySelector('.note-wrap-in');
  const noteItems = Array.from(noteWrapIn.children);

  // 要素を複製して無限ループを実現
  const duplicateItems = noteItems.map(item => item.cloneNode(true));
  duplicateItems.forEach(item => noteWrapIn.appendChild(item));

  let scrollAmount = 0;
  const scrollSpeed = 0.2; // スクロール速度 (px)

  function loopScroll() {
    scrollAmount -= scrollSpeed;
    if (Math.abs(scrollAmount) >= noteWrapIn.scrollWidth / 2) {
      // スクロール位置をリセット
      scrollAmount = 0;
    }
    noteWrapIn.style.transform = `translateX(${scrollAmount}px)`;
    requestAnimationFrame(loopScroll);
  }

  loopScroll();
});

</script>
</x-top-layout>

