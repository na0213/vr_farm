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
            <div class="flex items-center justify-start">
                <div class="mypage mt-10 ml-10 w-1/6">
                    <img src="{{ $mypage->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
                </div>
                <p class="ml-5">{{ $mypage->nickname ?? '名無しさん' }}</p>
                <a href="{{ route('user.farm.show', ['id' => $farm->id]) }}">
                    <button class="ml-10 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                        設定する
                    </button>
                </a>
            </div>

            <div class="mt-10 mx-5">
                <form method="POST" action="{{ route('posts.store') }}">
                    @csrf
                    <input type="hidden" name="farm_id" value="{{ $farm->id }}" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    <input type="text" name="post_title" :value="old('post_title')" placeholder="タイトル(無記入OK)" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    <textarea name="post_content" placeholder="本文" class="block mt-5 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"></textarea>
                    <button type="submit" class="mt-10 bg-transparent hover:bg-orange-500 text-orange-700 font-semibold hover:text-white py-2 px-4 border border-orange-500 hover:border-transparent rounded">投稿する</button>
                </form>
            </div>

            <div class="mt-10 mx-5">
            <!-- 投稿された内容を表示 -->
            @forelse ($posts as $post)
                <div class="mt-4 p-4 border rounded">
                    <h3>{{ $post->post_title }}</h3>
                    <p>{{ $post->post_content }}</p>
                    <small>投稿者: {{ $post->mypage->nickname ?? '名無しさん' }}</small>
                </div>
            @empty
                <p>投稿はありません。</p>
            @endforelse
            </div>
        </div>
    </div>

</x-app-layout>