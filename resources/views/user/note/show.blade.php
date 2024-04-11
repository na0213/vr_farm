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
        <svg class="w-7 h-7 text-gray-500 dark:text-gray-400 mb-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
            <path d="M18 5h-.7c.229-.467.349-.98.351-1.5a3.5 3.5 0 0 0-3.5-3.5c-1.717 0-3.215 1.2-4.331 2.481C8.4.842 6.949 0 5.5 0A3.5 3.5 0 0 0 2 3.5c.003.52.123 1.033.351 1.5H2a2 2 0 0 0-2 2v3a1 1 0 0 0 1 1h18a1 1 0 0 0 1-1V7a2 2 0 0 0-2-2ZM8.058 5H5.5a1.5 1.5 0 0 1 0-3c.9 0 2 .754 3.092 2.122-.219.337-.392.635-.534.878Zm6.1 0h-3.742c.933-1.368 2.371-3 3.739-3a1.5 1.5 0 0 1 0 3h.003ZM11 13H9v7h2v-7Zm-4 0H2v5a2 2 0 0 0 2 2h3v-7Zm6 0v7h3a2 2 0 0 0 2-2v-5h-5Z"/>
        </svg>
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