<x-app-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('user.mypage.show') }}">
                <h2 class="text-xl text-gray-600 dark:text-gray-200 leading-tight">
                    戻る
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                ノート
            </h2>
        </div>
    </x-slot>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="m-10 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
        <img class="p-8 rounded-t-lg" src="{{ $note->note_image }}" alt="product image" />
        <div class="p-2 mx-auto">
            <div class="font-bold text-xl">{{ $note->note_title }}</div>
       </div>
       <div class="p-2 mx-auto">
           <div class="text-ss">{!! $note->note_content !!}</div>
      </div>
    </div>

    <div class="flex justify-center">
        <div class="wrap m-10">
            <form action="{{ route('user.note.edit', ['note' => $note->id]) }}">
                <button type="submit" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    編集
                </button>
            </form>
        </div>
        <div class="wrap m-10">
            <form action="{{ route('user.note.destroy', $note->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('本当に削除してよろしいですか？');" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">削除</button>
            </form>
        </div>       
    </div>

</x-app-layout>