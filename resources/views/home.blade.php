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

<!-- ページネーションリンク -->
<div class="pagination">
  @if ($articles->onFirstPage())
    <span class="disabled">前へ</span>
  @else
    <a href="{{ $articles->previousPageUrl() }}">前へ</a>
  @endif

  @foreach ($articles->getUrlRange(1, $articles->lastPage()) as $page => $url)
    @if ($page == $articles->currentPage())
      <span class="active">{{ $page }}</span>
    @else
      <a href="{{ $url }}">{{ $page }}</a>
    @endif
  @endforeach

  @if ($articles->hasMorePages())
    <a href="{{ $articles->nextPageUrl() }}">次へ</a>
  @else
    <span class="disabled">次へ</span>
  @endif
</div>

</x-top-layout>

