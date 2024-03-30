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
                </div>
            @empty
                <p>投稿はありません。</p>
            @endforelse
        </div>
        
    </div>

</x-app-layout>