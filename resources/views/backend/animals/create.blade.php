<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
                <h2 class="text-xl text-gray-600 dark:text-gray-200 leading-tight">
                    戻る
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                特徴登録
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.backend.animals.store', ['farm' => $farm->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                        <label for="name" class="leading-7 text-sm text-gray-600">タイトル</label>
                        <input type="text" id="animal_name" name="animal_name" value="{{ old('animal_name')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>

                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="animal_info" class="leading-7 text-sm text-gray-600">内容</label>
                        <textarea name="animal_info" id="animal_info" cols="30" rows="10" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex-container m-5">
                    <div class="image-input w-4/5 mx-auto">
                        <label for="animal_image" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">画像<span class="text-red-600">（3MB以下）</span></label>
                        <input type="file" name="animal_image" id="animal_image" accept="image/*" onchange="previewImage(this, 'preview_image')" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        
                        {{-- ▼▼▼ 追加: 360° VR画像のチェックボックス ▼▼▼ --}}
                        <div class="mt-3 flex items-center">
                            <input type="checkbox" id="is_vr" name="is_vr" value="1" class="w-4 h-4 text-yellow-600 bg-gray-100 border-gray-300 rounded focus:ring-yellow-500 focus:ring-2">
                            <label for="is_vr" class="ml-2 text-sm font-medium text-gray-900">この画像を360°(VR)モードで表示する</label>
                        </div>
                        {{-- ▲▲▲ 追加ここまで ▲▲▲ --}}

                        <div class="image-preview mt-2" id="preview_image">
                            <img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" class="image-preview" style="width: 200px; height: auto;">
                        </div>
                    </div>
                </div>
                
                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録</button>
                </div>
            </form>

            <div class="mt-10 border-t pt-10">
                <h3 class="text-lg font-bold text-gray-700 mb-4 text-center">登録済みの特徴一覧</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($animals as $animal)
                        <div class="bg-white p-4 rounded shadow relative">
                            {{-- ▼▼▼ 追加: VRの場合にバッジを表示 ▼▼▼ --}}
                            @if($animal->is_vr)
                                <span class="absolute top-2 right-2 bg-yellow-500 text-white text-xs font-bold px-2 py-1 rounded z-10 shadow">360° VR</span>
                            @endif
                            {{-- ▲▲▲ 追加ここまで ▲▲▲ --}}

                            @if($animal->animal_image)
                                <img src="{{ $animal->animal_image }}" alt="{{ $animal->animal_name }}" class="w-full h-40 object-cover rounded mb-2">
                            @else
                                <div class="w-full h-40 bg-gray-200 rounded mb-2 flex items-center justify-center text-gray-500">No Image</div>
                            @endif
                            <h4 class="font-bold text-lg">{{ $animal->animal_name }}</h4>
                            <p class="text-sm text-gray-600 mt-1 line-clamp-3">{{ $animal->animal_info }}</p>
                            
                            <div class="mt-4 flex justify-between">
                                {{-- 編集ボタン --}}
                                <a href="{{ route('admin.backend.animals.edit', $animal->id) }}" class="text-blue-500 hover:underline">編集</a>
                                
                                {{-- 削除ボタン --}}
                                <form action="{{ route('admin.backend.animals.destroy', $animal->id) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline">削除</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($animals->isEmpty())
                    <p class="text-center text-gray-500">まだ登録されていません。</p>
                @endif
            </div>
        </div>
    </div>
    <script>
        function previewImage(input, previewId) {
            const maxFileSize = 3 * 1024 * 1024; // 3MB
    
            if (input.files && input.files[0]) {
                // ファイルサイズチェック
                if (input.files[0].size > maxFileSize) {
                    alert('ファイルサイズは3MB以下にしてください。');
                    input.value = ''; 
                    document.getElementById(previewId).innerHTML = '<img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" style="width: 200px; height: auto;">';
                    return;
                }
    
                var reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById(previewId).innerHTML = '<img src="' + e.target.result + '" alt="Image preview" style="width: 200px; height: auto;">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-admin-layout>