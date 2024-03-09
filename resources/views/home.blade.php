<x-top-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="slide-title">「いただきます」<br>から食を知る。</div>
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

    <a href="{{ route('farm.index') }}">
        <div class="fuwafuwa">
            <div class="farm_link">牧場へ行く</div><br><br><br>
            <p class="farm_link">WELCOME</p>
        </div>
    </a>

    <div class="wave">
      <div class="story">
        <p>STORY</p>
        <p class="story_text">いただく生命に感謝をする。<br>
          「いただきます」<br>
          この言葉は日本でうまれた言葉。<br>
          他の国にはない言葉。<br><br>
          「いただきます」をもう一度考える。<br>
          生命が生きている時にも関心を向けてみる。<br><br>
          人・動物・環境が生命とともに循環する。<br>
          そのストーリーを「いただこう」
        </p>
      </div>
    </div>


    <div class="box">
      <div class="text">
        <h3>見出しが入ります</h3>
        <p>親譲りの無鉄砲で小供の時から損ばかりしている。</p>
      </div>
      <div class="pict"><img src="{{asset('storage/top2.jpg')}}" alt=""></div>
    </div>
    
    <div class="box">
      <div class="text">
        <h3>見出しが入ります</h3>
        <p>親譲りの無鉄砲で小供の時から損ばかりしている。</p>
      </div>
      <div class="pict"><img src="{{asset('storage/top2.jpg')}}" alt=""></div>
    </div>
    <div class="box">
      <div class="text">
        <h3>見出しが入ります</h3>
        <p>親譲りの無鉄砲で小供の時から損ばかりし
</x-top-layout>
