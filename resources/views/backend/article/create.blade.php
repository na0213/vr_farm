<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            記事の新規作成
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.backend.article.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label for="farm_id" class="block text-gray-700 text-sm font-bold mb-2">牧場を選択</label>
                    <select name="farm_id" id="farm_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        @foreach ($farms as $farm)
                            <option value="{{ $farm->id }}">{{ $farm->farm_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">タイトル</label>
                    <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                </div>

                <div class="mb-4">
                    <div class="p-4 w-full mx-auto">
                        <div class="form-group">
                            <h1 style="text-align: center;">牧場記事</h1>
                            <div class="col-md-12">
                                <textarea id="editor1" name="editor1"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- 画像アップロード -->
                @for ($i = 0; $i < 5; $i++)
                <div class="mb-4">
                    <label for="upload_image{{ $i + 1 }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">画像 {{ $i + 1 }} <span class="text-red-600">（3MB以下）</span></label>
                    <input type="file" name="article_images[]" id="upload_image{{ $i + 1 }}" accept="image/*" onchange="previewImage(this, 'preview_image{{ $i + 1 }}')" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                    <div class="image-preview mt-2" id="preview_image{{ $i + 1 }}">
                        <img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" style="width: 200px; height: auto;">
                    </div>
                </div>
                @endfor

                <div class="-m-2">
                    <div class="p-4 w-full mx-auto">
                        <div class="relative flex justify-around">
                            <div>
                                <input type="radio" name="is_published" value="1" class="mr-2" checked>公開
                            </div>
                            <div>
                                <input type="radio" name="is_published" value="0" class="mr-2">非公開
                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">
                        登録する
                    </button>
                </div>
            </form>
            <div class="p-2 w-full flex justify-around mt-4">
                <a href="{{ route('admin.backend.owners.index') }}">
                <button type="button" class="text-white bg-gray-500 border-0 py-2 px-8 focus:outline-none hover:bg-gray-600 rounded text-lg">戻る</button>
                </a>
            </div>
        </div>
    </div>
    <script>
        // 画像プレビュー機能
        function previewImage(input, previewId) {
            const maxFileSize = 3 * 1024 * 1024; // 3MBをバイト単位で定義

            if (input.files && input.files[0]) {
                // ファイルサイズチェック
                if (input.files[0].size > maxFileSize) {
                    alert('ファイルサイズは3MB以下にしてください。');
                    input.value = ''; // 選択されたファイルをクリア
                    document.getElementById(previewId).innerHTML = '<img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" style="width: 200px; height: auto;">'; // プレビューをデフォルト画像にリセット
                    return;
                }

                // ファイルサイズが3MB以下の場合、画像をプレビュー
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).innerHTML = '<img src="' + e.target.result + '" alt="Image preview" style="width: 200px; height: auto;">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <script>
        CKEDITOR.replace('editor1', {
            height: 350, // エディタの高さ設定
            toolbar: [
                { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
                { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
                { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
                '/',
                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat'] },
                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote'] },
                { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
                { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar'] },
                '/',
                { name: 'styles', items: ['Styles', 'Format'] },
                { name: 'tools', items: ['Maximize'] }
            ],
            removeButtons: 'Source,Save,NewPage,Preview,Print,Templates,Cut,Copy,Paste,Undo,Redo', // 不要なボタンを削除
            allowedContent: true // HTMLの全ての要素を許可
        });

        // フォーム送信時にCKEditorの内容をtextareaに同期させる
        document.querySelector('form').addEventListener('submit', function(e) {
            for (const instance in CKEDITOR.instances) {
                CKEDITOR.instances[instance].updateElement();
            }
        });
    </script>
</x-admin-layout>