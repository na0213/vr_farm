<x-shop-layout>
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

    <a href="{{ route('user.farm.index') }}">
        <div class="fuwafuwa">
            {{-- <div class="farm_link">牧場へ行く</div><br><br><br> --}}
            {{-- <p class="farm_link">WELCOME</p> --}}
        </div>
    </a>
    <div class="wave">
      <div class="story">
        <p>CONCEPT</p>
        <p class="con_text">
          「アニマルウェルフェア」<br>
          「放牧」<br>
          「循環型」<br>
          人と動物と環境にやさしい牧場があります。<br>
          日本の未来に大切な牧場です。<br><br>
          毎日いただいているお肉・卵・牛乳。<br>
          月に１度でも。<br>
          自分にも動物にも環境にも<br>
          ちょっといいコトを。<br>
          とってもいいモノを。<br>
        </p>
      </div>
    </div>

    <div class="w-full mt-10">
      <div class="flex flex-col md:flex-row">
        <div class="m-10 md:w-2/5">
          <img src="{{asset('storage/concept.jpg')}}" loading="lazy" alt="Photo by Martin Sanchez" class="h-full w-full object-cover object-center" />
        </div>
        <div class="story md:w-3/5">
          <p>STORY</p>
          <p class="con_text">
            いただく命に感謝をする。<br>
            「いただきます」<br>
            この言葉は日本でうまれた言葉。<br><br>
            「いただきます」<br>
            命が生きている時にも関心を向けてみる。<br><br>
            人・動物・環境が生命とともに循環する。<br>
            そのストーリーを「いただこう」<br>
          </p>
        </div>
      </div>
    </div>
    <div class="story my-5">
      <p>NEWS</p>
    </div>
    <p class="mb-3 mx-5 flex items-center justify-center">「アニマルウェルフェア」「放牧」「循環型」「グラスフェッド」などに関する旬なニュースをお届け</p>
      <div class="mx-auto max-w-screen-lg flex flex-wrap justify-center">
        @foreach($news as $data)
        <div class="max-w-xs m-2 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
          <img class="w-full" src="{{ $data['thumbnail'] ? $data['thumbnail'] : asset('storage/noimage.jpg') }}">
          <div class="p-3">
            {{$data['name']}}
          <div class="mt-4">
            <a href="{{$data['url']}}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
              もっと読む
              <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
              </svg>
            </a>
          </div>
          </div>
        </div>
        @endforeach
    </div>
</x-shop-layout>
