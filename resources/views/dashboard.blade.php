<x-app-layout>
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

    <a href="{{ route('user.farm.index') }}">
        <div class="fuwafuwa">
            {{-- <div class="farm_link">牧場へ行く</div><br><br><br> --}}
            {{-- <p class="farm_link">WELCOME</p> --}}
        </div>
    </a>
    <div class="wave">
      <div class="story">
        <p>STORY</p>
        <p class="story_text">いただく命に感謝をする。<br>
          「いただきます」<br>
          この言葉は日本でうまれた言葉。<br><br>
          「いただきます」<br>
          命が生きている時にも関心を向けてみる。<br><br>
          人・動物・環境が生命とともに循環する。<br>
          そのストーリーを「いただこう」
        </p>
      </div>
    </div>

    <div class="w-full mt-10">
      <div class="flex flex-col md:flex-row">
        <div class="m-10 md:w-2/5">
          <img src="{{asset('storage/concept.jpg')}}" loading="lazy" alt="Photo by Martin Sanchez" class="h-full w-full object-cover object-center" />
        </div>
        <div class="story md:w-3/5">
          <p>CONCEPT</p>
          <p class="con_text">
            「アニマルウェルフェア」<br>
            「放牧」<br>
            「放牧」<br>
            人と動物と環境にやさしい循環型牧場があります<br><br>
            そのような牧場を大切にしたい。<br>
            日本の未来に残していきたい。<br><br>
            毎日いただいているお肉・卵・牛乳。<br>
            意識が変わるきっかけとなりたい。<br><br>
            月に１度でもいい。<br>
            循環型牧場の製品をいただくことで<br>
            自分にも動物にも環境にも<br>
            ちょっといいコトを。<br>
            とってもいいモノを。<br>
            皆さまに。<br>
          </p>
        </div>
      </div>
    </div>

</x-app-layout>
