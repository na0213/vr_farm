<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-12">
        <div class="max-w-sm w-full lg:max-w-full lg:flex">
            <div class="mt-10 mx-5">
                @if($mypage)
                    <!-- Mypageが存在する場合、編集画面に遷移するリンクを設定 -->
                    <a href="{{ route('user.mypage.edit', ['mypage' => $mypage->id]) }}">
                        <button class="ml-10 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                            編集する
                        </button>
                    </a>
                @else
                    <!-- Mypageが存在しない場合、作成画面に遷移するリンクを設定 -->
                    <a href="{{ route('user.mypage.create') }}">
                        <button class="ml-10 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                            設定する
                        </button>
                    </a>
                @endif
            </div>
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
        </div>
        <div class="m-20 flex items-center justify-start">
            <p class="ml-5">{{ $mypage->catchphrase ?? '' }}</p>
        </div>

        <div class="mt-10 mx-5">
            <h2 class="text-lg font-semibold">投稿一覧</h2>
            @forelse ($posts as $post)
                <div class="mt-4 p-4 border rounded flex justify-between items-center">
                    <div>
                        <h3>{{ $post->post_title }}</h3>
                        <p>{{ $post->post_content }}</p>
                    </div>
                    <form action="{{ route('posts.destroy', $post->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('本当に削除してよろしいですか？');" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                            削除
                        </button>
                    </form>
                </div>
            @empty
                <p>投稿はありません。</p>
            @endforelse
        </div>
        
    </div>

</x-app-layout>