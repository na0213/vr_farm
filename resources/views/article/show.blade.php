<x-top-layout>
<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />
<nav aria-label="breadcrumb" class="breadcrumb-font mt-10">
  {!! Breadcrumbs::render('article.show', $article) !!}
</nav>
<div class="article-container">
  <!-- 画像スライドショー -->
  <ul class="slideshow-fade">
    @if($article->article_images)
    @foreach(json_decode($article->article_images) as $image)
    <li>
      <div class="image-container" style="background-image: url('{{ $image }}');"></div>
    </li>
    @endforeach
    @else
    <p>No images available</p>
    @endif
  </ul>
  <!-- タイトルとテキストは画像スライドの外に表示 -->
  <div class="text-container">
    <h2>{{ $article->title }}</h2>
    <div>{!! $article->article_content !!}</div>
  </div>
</div>
    

<script>
  $(function(){
  $(".slideshow-fade li").css({"position":"relative","overflow":"hidden"});
  $(".slideshow-fade li").hide().css({"position":"absolute","top":0,"left":0});
  $(".slideshow-fade li:first").addClass("fade").show();
    // 最初のスライドのみ即時に切り替える
    setTimeout(function() {
      var $active = $(".slideshow-fade li.fade");
      var $next = $active.next("li").length ? $active.next("li") : $(".slideshow-fade li:first");
      $active.fadeOut(3000).removeClass("fade");
      $next.fadeIn(2000).addClass("fade");
    }, 1000); // 初回は即時実行

    // 以降のスライドは指定した間隔で切り替え
    setInterval(function(){
      var $active = $(".slideshow-fade li.fade");
      var $next = $active.next("li").length ? $active.next("li") : $(".slideshow-fade li:first");
      $active.fadeOut(3000).removeClass("fade");
      $next.fadeIn(2000).addClass("fade");
    }, 5000); // 切り替え間隔を短縮
});
</script>
  
</x-top-layout>
  