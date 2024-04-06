<x-guestfarm-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-sm w-full lg:max-w-full lg:flex">
                <a href="{{ route('farm.show', ['id' => $farm->id]) }}">
                    <button class="ml-10 bg-transparent hover:bg-gray-500 text-gray-700 font-semibold hover:text-white py-2 px-4 border border-gray-500 hover:border-transparent rounded">
                        {{ $farm->farm_name }}へ戻る
                    </button>
                </a>
            </div>

            <div class="mt-10 mx-5 text-2xl">
                <span class="mr-2">{{ $farm->farm_name }}</span><i class="far fa-heart text-green-500"></i><span class="ml-2">コミュニティ</span>
            </div>
            
            <div class="swiper-container mt-10 mx-5">
                <div class="swiper-wrapper">
                    @foreach($farmImages as $image)
                        <div class="swiper-slide">
                            <img src="{{ asset($image->image_path) }}" alt="Farm Image">
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>

            <div class="mx-5 mt-10">
                <div>みんなの投稿<br>
                ログインすると投稿できるよ！</div>
                @forelse ($allPosts as $post)
                <div class="mt-4 p-4 border rounded {{ $post->is_owner ? 'bg-green-100' : '' }}">

                    {{-- <div class="mt-4 p-4 border rounded {{ $post->is_owner ? 'bg-green-100' : (optional($post->mypage)->user_id == auth()->user()->id ? 'bg-blue-100' : '') }}"> --}}
                        <h3>{{ $post->post_title }}</h3>
                        <p>{{ $post->post_content }}</p>
                        <div class="flex">
                            <!-- ここでmypageの存在とis_publishedを確認 -->
                            @if(!$post->is_owner && $post->mypage && $post->mypage->is_published)
                                {{-- <a href="{{ route('user.mypage.public_show', ['mypage' => $post->mypage->id]) }}" class="custom-link"> --}}
                                <div class="flex">
                                    @if($post->image)
                                        <small class="icon_pic">
                                            <img src="{{ $post->image }}" alt="Image" class="w-1/6">
                                        </small>
                                    @endif
                                    <small>投稿者: {{ $post->is_owner ? $farm->farm_name : $post->mypage->nickname }}</small>
                                </div>
                                {{-- </a> --}}
                            @else
                                @if($post->image)
                                    <small class="icon_pic">
                                        <img src="{{ $post->image }}" alt="Image" class="w-1/6">
                                    </small>
                                @endif
                                <small>投稿者: {{ $post->is_owner ? $farm->farm_name : '名無しさん' }}</small>
                            @endif
                        </div>
                    </div>
                @empty
                    <p>投稿はありません。</p>
                @endforelse
            </div>
        </div>
    </div>
   

</x-guestfarm-layout>