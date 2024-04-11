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

    <div class="wrap mt-10">
        <form action="{{ route('user.note.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="-m-2">
                <div class="p-2 w-4/5 mx-auto">
                    <div class="relative">
                    <label for="note_title" class="leading-7 text-sm text-gray-600">タイトル</label>
                    <input type="text" id="note_title" name="note_title" value="{{ old('note_title')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                    </div>
                </div>
            </div>

            <div class="-m-2">
                <div class="p-4 w-full mx-auto">
                    <div class="form-group">
                        <h1 style="text-align: center;">日記（3000字まで）</h1>
                        <div class="col-md-12">        
                            <textarea name="editor1">{{ old('note_content') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <button class="mt-10 ml-10 bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                登録する
            </button>
        </form>
    </div>
    <script>
        CKEDITOR.replace( 'editor1',{
            height:350,
            removeButtons:'Image, Unlink,Anchor, NewPage,DocProps,Preview,Print,Templates,Cut,Copy,Paste,PasteText,PasteFromWord,Undo,Redo,Find,Replace,SelectAll,Scayt,RemoveFormat,Outdent,Indent,Blockquote,Styles,About'
        });
        // フォームが送信される時にCKEditorの内容をtextareaに同期させる
        document.querySelector('form').addEventListener('submit', function() {
            CKEDITOR.instances.editor1.updateElement();
        });
    </script>
</x-app-layout>