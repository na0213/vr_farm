<x-masterfarm-layout>
    <x-slot name="header">
        <a href="{{ route('owner.backend.masters.show', ['id' => $farm->owner->farm->id]) }}">
            <h2 class="font-semibold text-xl text-gray-500 dark:text-gray-200 leading-tight">
                ◀︎プレビュー
            </h2>
        </a>
    </x-slot>
    <div class="wrap mt-10">
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
        <div class="mt-10 mx-5">
            <form method="POST" action="{{ route('owner.ownerposts.store') }}">
                @csrf
                <input type="text" name="post_title" :value="old('post_title')" placeholder="タイトル(無記入OK)" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                <textarea name="post_content" placeholder="本文" class="block mt-5 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"></textarea>
                <button type="submit" class="mt-10 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">投稿する</button>
            </form>

            <div class="mx-5 mt-10">
                <div>みんなの投稿</div>
                @forelse ($allPosts as $post)
                <div class="mt-4 p-4 border rounded {{ $post->is_owner ? 'bg-green-100' : '' }}">

                        <h3>{{ $post->post_title }}</h3>
                        <p>{{ $post->post_content }}</p>
                        <div class="flex">
                            <!-- ここでmypageの存在とis_publishedを確認 -->
                            @if(!$post->is_owner && $post->mypage && $post->mypage->is_published)
                                <a href="{{ route('owner.ownerposts.usershow', ['mypage' => $post->mypage->id]) }}" class="custom-link">
                                <div class="flex">
                                    @if($post->image)
                                        <small class="icon_pic">
                                            <img src="{{ $post->image }}" alt="Image" class="w-1/6">
                                        </small>
                                    @endif
                                    <small>投稿者: {{ $post->is_owner ? $farm->farm_name : $post->mypage->nickname }}</small>
                                </div>
                                </a>
                            @else
                                @if($post->image)
                                    <small class="icon_pic">
                                        <img src="{{ $post->image }}" alt="Image" class="w-1/6">
                                    </small>
                                @endif
                                <small>投稿者: {{ $post->is_owner ? $farm->farm_name : '名無しさん' }}</small>
                            @endif
                        </div>

                        @if ($post->is_owner)
                        <form action="{{ route('owner.ownerposts.destroy', ['ownerpost' => $post->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('本当に削除してよろしいですか？');" class="mt-2 bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">削除</button>
                        </form>
                        @endif
                    </div>
                @empty
                    <p>投稿はありません。</p>
                @endforelse
            </div>
        </div>
    </div>
</x-masterfarm-layout>