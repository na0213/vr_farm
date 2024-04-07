<x-masterfarm-layout>
    <x-slot name="header">
        <a href="{{ route('owner.backend.masters.posts', ['farm' => $farm->id]) }}">
            <h2 class="font-semibold text-xl text-gray-500 dark:text-gray-200 leading-tight">
                ◀コミュニティ
            </h2>
        </a>
    </x-slot>
    <div class="py-12">
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
</x-masterfarm-layout>