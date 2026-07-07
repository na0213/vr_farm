<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="icon" href="{{ asset('storage/favicon.png') }}" type="image/x-icon">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            // ページ側から <x-slot name="title"> 等で上書きできる(未指定ならサイト共通の既定値)
            $pageTitle = isset($title) && trim($title) !== '' ? trim($title) . ' | ウェルフェアFARM' : 'ウェルフェアFARM | しあわせな牧場の、おいしいもの。';
            $pageDescription = isset($metaDescription) && trim($metaDescription) !== '' ? trim($metaDescription) : '放牧卵、放牧豚、グラスフェッド乳製品。動物がのびのび育つ牧場の「おいしい理由」と、お取り寄せ情報をお届けします。';
            $pageOgImage = isset($ogImage) && trim($ogImage) !== '' ? trim($ogImage) : asset('storage/sns.jpg');
        @endphp
        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:image" content="{{ $pageOgImage }}">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="robots" content="index, follow">
        <!-- 構造化データ -->
        <script type="application/ld+json">
          {
              "@@context": "https://schema.org",
              "@type": "WebSite",
              "name": "ウェルフェアFARM",
              "url": "https://www.farm360.jp",
              "description": "放牧やアニマルウェルフェアなど、人にも動物にも環境にも優しい牧場情報をお届け！",
              "publisher": {
                  "@type": "Organization",
                  "name": "ウェルフェアFARM運営"
              }
          }
          </script>
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.css"/>
        {{-- <script src="https://cdn.ckeditor.com/4.14.0/standard/ckeditor.js"></script> --}}
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
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/pannellum@2.5.6/build/pannellum.js"></script>
    </body>
    
    @include('layouts.partials.footer')
  
</html>
