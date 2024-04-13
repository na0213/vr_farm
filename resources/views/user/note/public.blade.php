<x-app-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="mypage ml-10 w-1/6">
        <img src="{{ $note->mypage->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
    </div>
    <div class="flex flex-col ml-5">
        <p>{{ $note->mypage->nickname ?? '名無しさん' }}</p>
        <p class="mt-5">
        @if(auth()->check() && auth()->user()->mypage->id != $note->mypage->id)
            @if(auth()->user()->mypage->following()->where('followed_id', $note->mypage->id)->exists())
                <form action="{{ route('mypage.unfollow', $note->mypage->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button class="bg-transparent hover:bg-gray-500 text-gray-700 font-semibold hover:text-white py-2 px-4 border border-gray-500 hover:border-transparent rounded">
                        フォローをやめる
                    </button>
                </form>
            @else
                <form action="{{ route('mypage.follow', $note->mypage->id) }}" method="POST">
                    @csrf
                    <button class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                        フォローする
                    </button>
                </form>
            @endif
        @endif
        </p>
    </div>
    

    <div class="m-10 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <img class="p-8 rounded-t-lg" src="{{ $note->note_image }}" alt="product image" />
        <div class="p-2 mx-auto">
            <div class="font-bold text-xl">{{ $note->note_title }}</div>
       </div>
       <div class="p-2 mx-auto">
           <div class="text-ss">{!! $note->note_content !!}</div>
      </div>
    </div>
    

</x-app-layout>