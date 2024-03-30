<x-master-layout>
    <div class="wrap mt-10">
        <div class="mt-10 mx-5 text-2xl">
            <span class="mr-2">{{ $farm->farm_name }}様</span><i class="far fa-bell text-green-500"></i><span class="ml-2">コミュニティ</span>
        </div>
        <div class="mt-10 mx-5">
            <form method="POST" action="{{ route('owner.ownerposts.store') }}">
                @csrf
                <input type="text" name="post_title" placeholder="タイトル(無記入OK)" class="block mt-1 w-full">
                <textarea name="post_content" placeholder="本文" class="block mt-5 w-full"></textarea>
                <button type="submit" class="mt-10 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">投稿する</button>
            </form>
            <div class="flex mt-10">
                <div class="w-1/2">
                    <h2>{{ $farm->farm_name }}様</h2>
                    @forelse ($ownerposts as $post)
                        <div class="p-4 border rounded">
                            <h3>{{ $post->post_title }}</h3>
                            <p>{{ $post->post_content }}</p>
                            <form action="{{ route('owner.ownerposts.destroy', $post->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('この投稿を削除してもよろしいですか？');" class="mt-3 bg-transparent hover:bg-red-500 text-red-700 font-semibold hover:text-white py-2 px-4 border border-red-500 hover:border-transparent rounded">削除</button>
                            </form>
                        </div>
                    @empty
                        <p>投稿までお待ちください！</p>
                    @endforelse
                </div>
            
                <div class="w-1/2">
                    <h2>みんなの投稿</h2>
                    @forelse ($posts as $post)
                        <div class="p-4 border rounded">
                            <h3>{{ $post->post_title }}</h3>
                            <p>{{ $post->post_content }}</p>
                            @if(optional($post->mypage)->is_published)
                                <div class="flex">
                                    <small class="icon_pic">
                                        <img src="{{ optional($post->mypage)->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
                                    </small>
                                    <small>投稿者: {{ optional($post->mypage)->nickname ?? 'Noname' }}</small>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p>投稿までお待ちください！</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-master-layout>