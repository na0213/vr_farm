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
            
            <div class="flex-container m-5">
                <div class="image-input">
                    <label for="note_image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">画像<span class="text-red-600">（3MB以下）</span></label>
                    <input type="file" name="note_image" id="note_image" accept="image/*" onchange="previewImage(this, 'preview_image')" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                    <div class="image-preview mt-2" id="preview_image">
                        <img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" class="image-preview">
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
    <script>
        function previewImage(input, previewId) {
            const maxFileSize = 3 * 1024 * 1024; // 3MBをバイト単位で定義
    
            if (input.files && input.files[0]) {
                // ファイルサイズチェック
                if (input.files[0].size > maxFileSize) {
                    // ファイルサイズが1MBを超える場合
                    alert('ファイルサイズは1MB以下にしてください。');
                    input.value = ''; // 選択されたファイルをクリア
                    document.getElementById(previewId).innerHTML = '<img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" style="width: 200px; height: auto;">'; // プレビューをデフォルト画像にリセット
                    return; // これ以上処理を続行しない
                }
    
                // ファイルサイズが1MB以下の場合、画像をプレビュー
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).innerHTML = '<img src="' + e.target.result + '" alt="Image preview" style="width: 200px; height: auto;">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>