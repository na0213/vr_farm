<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('storage/favicon.png') }}" type="image/x-icon">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>牧場いきたい - 全国の牧場検索サイト | 人にも動物にも環境にも優しい「放牧」「アニマルウェルフェア」「循環型」牧場を探す</title>
        <meta name="description" content="放牧やアニマルウェルフェアなど、人にも動物にも環境にも優しい牧場情報をお届け！全国の牧場を巡り、体験や取り組みを知ってみませんか？">
        <meta name="keywords" content="牧場, 僕樹見学, 家族旅行　牧場, 牧場体験, 放牧, アニマルウェルフェア, 循環型, サステナブル, 牧場体験">
        <meta property="og:title" content="牧場いきたい - 全国の牧場検索サイト | 人にも動物にも環境にも優しい牧場情報をお届け">
        <meta property="og:description" content="放牧やアニマルウェルフェアなど、人にも動物にも環境にも優しい牧場情報をお届け！全国の牧場を巡り、体験や取り組みを知ってみませんか？">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ asset('storage/sns.jpg') }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="robots" content="index, follow">
        <!-- 構造化データ -->
        <script type="application/ld+json">
          {
              "@context": "https://schema.org",
              "@type": "WebSite",
              "name": "牧場いきたい",
              "url": "https://www.farm360.jp",
              "description": "放牧やアニマルウェルフェアなど、人にも動物にも環境にも優しい牧場情報をお届け！",
              "publisher": {
                  "@type": "Organization",
                  "name": "牧場いきたい運営"
              }
          }
          </script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script>
        <!-- Scripts -->
        @vite(['resources/css/app.css','resources/css/top.css', 'resources/js/app.js'])
        <script type="text/javascript"src="//code.typesquare.com/static/ZDbTe4IzCko%253D/ts106f.js"charset="utf-8"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-rwdImageMaps/1.6/jquery.rwdImageMaps.js" integrity="sha256-MV/L2nrfaNYQUtnDja7Ok3sF1D5Rpviw/MUs76PX9nE=" crossorigin="anonymous"></script>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-40PSBGCN3Y"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'G-40PSBGCN3Y');
        </script>
    </head>
    <body>
        {{-- <div class="min-h-screen bg-gray-100"> --}}
            @include('layouts.guest-navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div>
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        {{-- </div> --}}

        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        @if (Route::is('index'))
          @vite(['resources/js/swiper.js'])
        @endif
        <script>
            $('img[usemap]').rwdImageMaps();
        </script>
    </body>
    
    @include('layouts.partials.footer')
  
</html>
