<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-sm w-full lg:max-w-full lg:flex">
                <a href="{{ route('user.farm.show', ['id' => $farm->id]) }}">
                    <button class="ml-10 bg-transparent hover:bg-gray-500 text-gray-700 font-semibold hover:text-white py-2 px-4 border border-gray-500 hover:border-transparent rounded">
                        {{ $farm->farm_name }}へ戻る
                    </button>
                </a>
            </div>
            @if (session('success'))
                <div class="ml-5 alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="mt-10 mx-5 text-2xl">
                <span class="mr-2">{{ $farm->farm_name }}様</span><i class="far fa-bell text-green-500"></i><span class="ml-2">コミュニティ</span>
            </div>
            <div class="flex items-center justify-start">
                <div class="mypage mt-10 ml-10 w-1/6">
                    <img src="{{ $mypage->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
                </div>
                <p class="ml-5">{{ $mypage->nickname ?? '名無しさん' }}</p>
                <a href="{{ route('user.mypage.show') }}">
                    <button class="ml-10 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                        設定する
                    </button>
                </a>
            </div>

            <div class="mt-10 mx-5">
                <form method="POST" action="{{ route('posts.store') }}">
                    @csrf
                    <div>そのまま投稿できるよ！牧場を盛り上げよう！</div>
                    <div>牧場ツアーの感想、商品の感想などなんでもOK！</div>
                    <div>生産者さんの声も聞けるかも！？</div>
                    <input type="hidden" name="farm_id" value="{{ $farm->id }}" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    <input type="text" name="post_title" :value="old('post_title')" placeholder="タイトル(無記入OK)" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    <textarea name="post_content" placeholder="本文" class="block mt-5 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"></textarea>
                    <button type="submit" class="mt-10 bg-transparent hover:bg-orange-500 text-orange-700 font-semibold hover:text-white py-2 px-4 border border-orange-500 hover:border-transparent rounded">投稿する</button>
                </form>
            </div>
            <div class="flex">
                <div class="w-1/2">
                    <div>生産者の投稿</div>
                    @forelse ($ownerposts as $post)
                        <div class="mt-4 p-4 border rounded">
                            <h3>{{ $post->post_title }}</h3>
                            <p>{{ $post->post_content }}</p>
                            <small>投稿者: {{ $farm->farm_name ?? '名無しさん' }}</small>
                        </div>
                    @empty
                        <p>投稿はありません。</p>
                    @endforelse
                </div>
            
                <div class="w-1/2">
                    <div>みんなの投稿</div>
                    @forelse ($posts as $post)
                        <div class="mt-4 p-4 border rounded {{ auth()->user()->id == $post->user_id ? 'bg-blue-100' : '' }}">
                            <h3>{{ $post->post_title }}</h3>
                            <p>{{ $post->post_content }}</p>
                            @if(optional($post->mypage)->is_published)
                                <a href="{{ route('user.mypage.public_show', ['mypage' => $post->mypage->id]) }}" class="custom-link">
                                    <small class="icon_pic">
                                        <img src="{{ optional($post->mypage)->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
                                    </small>
                                    <small>投稿者: {{ optional($post->mypage)->nickname ?? '名無しさん' }}</small>
                                </a>
                            @else
                                <small>投稿者: {{ '名無しさん' }}</small>
                            @endif
                        </div>
                    @empty
                        <p>投稿はありません。</p>
                    @endforelse
                </div>
            </div>
            
            {{-- <div class="mt-10 mx-5">
            <div>みんなの投稿</div>
            @forelse ($posts as $post)
                <div class="mt-4 p-4 border rounded">
                    <h3>{{ $post->post_title }}</h3>
                    <p>{{ $post->post_content }}</p>
                    @if(optional($post->mypage)->is_published)
                        <a href="{{ route('user.mypage.public_show', ['mypage' => $post->mypage->id]) }}" class="custom-link">
                            <div class="flex">
                                <small class="icon_pic">
                                    <img src="{{ optional($post->mypage)->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image" class="w-1/6">
                                </small>
                                <small class="flex items-center justify-start">投稿者: {{ optional($post->mypage)->nickname ?? '名無しさん' }}</small>
                            </div>
                        </a>
                    @else
                        <div class="flex">
                            <small class="icon_pic">
                                <img src="{{ asset('storage/noimage.jpg') }}" alt="Mypage Image" class="w-1/6">
                            </small>
                            <small class="flex items-center justify-start">投稿者: {{ '名無しさん' }}</small>
                        </div>
                    @endif
                </div>
            @empty
                <p>投稿はありません。</p>
            @endforelse
            </div> --}}

        </div>
    </div>

</x-app-layout>