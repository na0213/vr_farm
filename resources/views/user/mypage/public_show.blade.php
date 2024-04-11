<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-12">
        @if (session('success'))
            <div class="ml-5 alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="flex items-center justify-start">
            <div class="mypage ml-10 w-1/6">
                <img src="{{ $mypage->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
            </div>
            <div class="flex flex-col ml-5">
                <p>{{ $mypage->nickname ?? '名無しさん' }}</p>
                <p class="mt-5">
                @if(auth()->check() && auth()->user()->mypage->id != $mypage->id)
                    @if(auth()->user()->mypage->following()->where('followed_id', $mypage->id)->exists())
                        <form action="{{ route('mypage.unfollow', $mypage->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="bg-transparent hover:bg-gray-500 text-gray-700 font-semibold hover:text-white py-2 px-4 border border-gray-500 hover:border-transparent rounded">
                                フォローをやめる
                            </button>
                        </form>
                    @else
                        <form action="{{ route('mypage.follow', $mypage->id) }}" method="POST">
                            @csrf
                            <button class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                                フォローする
                            </button>
                        </form>
                    @endif
                @endif
                </p>
            </div>
        </div>
        <div class="m-10 flex items-center justify-start">
            <p class="ml-5">{!! nl2br(e($mypage->catchphrase ?? '')) !!}</p>
        </div>

        <div class="mt-10 mx-5">
            <h2 class="text-lg font-semibold">投稿一覧</h2>
            @forelse ($posts as $post)
                <div class="mt-4 p-4 border rounded flex justify-between items-center">
                    <div>
                        <h3>{{ $post->post_title }}</h3>
                        <p>{{ $post->post_content }}</p>
                        <p class="mt-3">{{ $post->farm->farm_name }}</p>
                    </div>
                </div>
            @empty
                <p>投稿はありません。</p>
            @endforelse
        </div>
        
    </div>

</x-app-layout>