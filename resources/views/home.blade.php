<x-top-layout>
  <!-- Session Status -->
  <x-auth-session-status class="mb-4" :status="session('status')" />

  {{-- <div class="slide-title">牧場から<br>食を知る</div> --}}
  <div class="swiper home-hero">
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

    <!-- Hero overlay -->
    <div class="hero-overlay reveal" data-animate>
      <p class="hero-eyebrow">牧場から、食を知る</p>
      <h1 class="hero-title">人・動物・環境にやさしい牧場へ</h1>
      <div class="hero-cta">
        <a href="{{ route('farm.index') }}" class="btn-primary" aria-label="牧場検索へ">
          牧場検索へ
        </a>
      </div>
      <div class="scroll-hint" aria-hidden="true">scroll</div>
    </div>

    <!-- playful tiny decorations around hero -->
    <div class="play-deco" aria-hidden="true">
      <span class="sparkle s1"></span>
      <span class="sparkle s2"></span>
      <span class="sparkle s3"></span>
      <span class="sparkle s4"></span>
    </div>
  </div>

  <div class="story reveal" data-animate>
    <p class="mt-20 text-[#e09885] with-icon"><span class="icon-leaf" aria-hidden="true"></span>STORY</p>
  </div>
  <div class="note-title reveal" data-animate>
    <p class="wavy-underline">思い</p>
  </div>
    <div class="story story-panel reveal" data-animate>
      <p class="con_text">
        「アニマルウェルフェア」<br>
        「放牧」<br>
        「循環型」<br>
        人・動物・環境が循環する牧場を巡る<br><br>
        牧場ごとの味をたんのうし<br>
        牧場のある風景へ足を運ぶ<br>
        <br>
        牧場のファンづくりを目指して<br>
        未来につながる暮らしと食を紡ぎたい<br>
        <br>
      </p>
      <div class="story-decoration">
        <div class="circ circ1"></div>
        <div class="circ circ2"></div>
        <div class="circ circ3"></div>
        <div class="circ circ4"></div>
      </div>
    </div>


  <div class="story reveal" data-animate>
    <p class="mt-10 text-[#e09885] with-icon"><span class="icon-leaf" aria-hidden="true"></span>FARM</p>
  </div>
  <div class="note-title reveal" data-animate>
    <p class="wavy-underline">牧場検索</p>
  </div>
  <div class="circle reveal" data-animate>
    <a href="{{ route('farm.index') }}">
    <div class="concept bg-image-1 concept-accent">
        <p class="center-text">
            <span class="line-1">推し牧場を</span>
            <span class="line-2">みつけよう！</span>
        </p>
        <span class="concept-badge" aria-hidden="true"></span>
    </div>
    </a>
  </div>

  <div class="story reveal" data-animate>
    <p class="mt-20 text-[#e09885] with-icon"><span class="icon-star" aria-hidden="true"></span>NOTE</p>
  </div>
  <div class="note-title reveal" data-animate>
    <p class="wavy-underline">訪問記・取材</p>
</div>
<div class="note-wrap reveal" data-animate>
  <div class="note-wrap-in home-note-grid">
    @foreach ($articles as $article)
      @php
        $images = json_decode($article->article_images, true); // JSONデコードを実行
        $firstImage = $images[0] ?? asset('storage/noimage.jpg'); // 画像がなければデフォルト画像を設定
      @endphp
      <div class="note-item">
        <a href="{{ route('article.show', $article->id) }}">
          <div class="pic">
            <img src="{{ $firstImage }}" alt="{{ $article->title }}">
          </div>
          <p>{{ $article->title }}</p>
          <a href="{{ route('article.show', ['id' => $article->id]) }}" class="more-link btn-link">もっとみる →</a>
        </a>
      </div>
    @endforeach
  </div>
</div>

<!-- ページネーションリンク -->
<div class="pagination reveal" data-animate>
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
