<x-top-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

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
        <p>{!! $article->article_content !!}</p>
      </div>
          
    </div>
    

<script>
  $(function(){
  $(".slideshow-fade li").css({"position":"relative","overflow":"hidden"});
  $(".slideshow-fade li").hide().css({"position":"absolute","top":0,"left":0});
  $(".slideshow-fade li:first").addClass("fade").show();
  setInterval(function(){
    var $active = $(".slideshow-fade li.fade");
    var $next = $active.next("li").length?$active.next("li"):$(".slideshow-fade li:first");
    $active.fadeOut(6000).removeClass("fade");
    $next.fadeIn(4000).addClass("fade");
  },6000);
});
</script>
  
</x-top-layout>
  