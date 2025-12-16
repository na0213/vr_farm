<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
                <h2 class="text-xl text-gray-600 dark:text-gray-200 leading-tight">
                    戻る
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                特徴編集
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 修正1: 画像送信のため POST メソッドを使用し、@method('PUT') は削除 --}}
            <form action="{{ route('admin.backend.animals.update_post', ['id' => $animal->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                {{-- @method('PUT') ← 削除しました --}}

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                            <label for="animal_name" class="leading-7 text-sm text-gray-600">タイトル</label>
                            <input type="text" id="animal_name" name="animal_name" value="{{ $animal->animal_name }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>

                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                            <label for="animal_info" class="leading-7 text-sm text-gray-600">内容</label>
                            <textarea name="animal_info" id="animal_info" cols="30" rows="10" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">{{ trim($animal->animal_info) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex-container m-10">
                    <div class="image-input p-2 w-4/5 mx-auto">
                        <label for="animal_image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">画像<span class="text-red-600">（3MB以下）</span></label>
                        <input type="file" name="animal_image" id="animal_image" accept="image/*" onchange="previewImage(this, 'preview_image')" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        
                        {{-- ▼▼▼ 追加: 360° VR画像のチェックボックス ▼▼▼ --}}
                        <div class="mt-3 flex items-center">
                            <input type="checkbox" id="is_vr" name="is_vr" value="1" 
                                class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 focus:ring-2"
                                @if($animal->is_vr) checked @endif
                            >
                            <label for="is_vr" class="ml-2 text-sm font-medium text-gray-900">この画像を360°(VR)モードで表示する</label>
                        </div>
                        {{-- ▲▲▲ 追加ここまで ▲▲▲ --}}

                        {{-- 修正2: 画像がない場合も考慮したプレビュー表示 --}}
                        <div class="image-preview mt-2" id="preview_image">
                            @if($animal->animal_image)
                                <p class="text-xs text-gray-500 mb-1">現在の画像:</p>
                                <img src="{{ $animal->animal_image }}" alt="Current Image" style="width: 200px; height: auto;" class="rounded shadow">
                            @else
                                <img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" style="width: 200px; height: auto;" class="rounded shadow opacity-50">
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">更新</button>
                </div>
            </form>

            {{-- 削除フォーム --}}
            <div class="p-2 w-full flex justify-around m-4 border-t pt-4">
                <form action="{{ route('admin.backend.animals.destroy', ['id' => $animal->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('本当に削除してよろしいですか？');" class="text-white bg-red-500 border-0 py-2 px-4 focus:outline-none hover:bg-red-600 rounded text-lg">削除</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input, previewId) {
            const maxFileSize = 3 * 1024 * 1024; // 3MB
    
            if (input.files && input.files[0]) {
                // ファイルサイズチェック
                if (input.files[0].size > maxFileSize) {
                    // 修正3: 文言を3MBに修正
                    alert('ファイルサイズは3MB以下にしてください。');
                    input.value = ''; 
                    // リセット時の画像を安全に表示
                    document.getElementById(previewId).innerHTML = '<img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" style="width: 200px; height: auto;" class="rounded shadow opacity-50">';
                    return; 
                }
    
                // 画像プレビュー
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).innerHTML = '<img src="' + e.target.result + '" alt="Image preview" style="width: 200px; height: auto;" class="rounded shadow">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>