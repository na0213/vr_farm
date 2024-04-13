<x-app-layout>
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

        <div class="mt-10 mx-5">
            <h2 class="mb-3 text-lg font-semibold">ノート一覧</h2>
            <div class="flex">
            @forelse ($notes as $note)
                <div class="mb-2 ml-2 max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
                        <img class="p-8 rounded-t-lg" src="{{ $note->note_image }}" alt="product image" />
                    <a href="#">
                        <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">{{ $note->note_title }}</h5>
                    </a>
                    <p class="mb-3 font-normal text-gray-700 dark:text-gray-400 note-content">{!! $note->note_content !!}</p>
                    <a href="{{ route('user.note.public', $note->id) }}" class="inline-flex items-center px-3 py-2 text-sm font-medium text-center text-white bg-yellow-500 rounded-lg hover:bg-yellow-600 focus:ring-4 focus:outline-none focus:ring-yellow-300 dark:bg-yellow-600 dark:hover:bg-yellow-700 dark:focus:ring-yellow-800">
                        読む
                        <svg class="rtl:rotate-180 w-3.5 h-3.5 ms-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 10">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M1 5h12m0 0L9 1m4 4L9 9"/>
                        </svg>
                    </a>
                </div>
            @empty
                <p>投稿はありません。</p>
            @endforelse
            </div>
        </div>
        
    </div>

</x-app-layout>