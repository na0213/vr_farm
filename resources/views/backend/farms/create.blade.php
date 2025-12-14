<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    オーナー
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                牧場登録
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.backend.farms.store', ['owner' => $owner->id]) }}" method="POST" enctype="multipart/form-data">
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
                        <label for="name" class="leading-7 text-sm text-gray-600">牧場名</label>
                        <input type="text" id="name" name="name" value="{{ old('name')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="catchcopy" class="leading-7 text-sm text-gray-600">キャッチコピー</label>
                        <input type="text" id="catchcopy" name="catchcopy" value="{{ old('catchcopy')}}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="kind" class="leading-7 text-sm text-gray-600">動物種類</label>
                        <div class="flex ">
                            @foreach($kinds as $kind)
                                <div class='appearance'>
                                    <input type='checkbox' id='kind{{ $kind->id }}' name='kinds[]' value="{{ $kind->id }}" class='checkbox' @if(is_array(old('kinds')) && in_array($kind->id, old('kinds'))) checked @endif>
                                    {{ $kind->kind }}
                                </div>
                            @endforeach
                        </div>
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="kind" class="leading-7 text-sm text-gray-600">牧場の特徴</label>
                        <div class="flex">
                            @foreach($keywords as $keyword)
                                <div class='appearance'>
                                    <input type='checkbox' id='kind{{ $keyword->id }}' name='keywords[]' value="{{ $keyword->id }}" class='checkbox' @if(is_array(old('keywords')) && in_array($keyword->id, old('keywords'))) checked @endif>
                                    {{ $keyword->keyword }}
                                </div>
                            @endforeach
                        </div>
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                            {{-- ラベル --}}
                            <label for="vr" class="leading-7 text-sm text-gray-600">
                                VR画像（360度画像）<span class="text-red-600">（10MB以下）</span>
                            </label>

                            {{-- ファイル入力（onchangeを追加） --}}
                            <input type="file" id="vr" name="vr" accept="image/*" 
                                onchange="previewImage(this, 'preview_vr')" 
                                class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                            
                            {{-- プレビュー表示エリア --}}
                            <div class="mt-2" id="preview_vr">
                                @if(isset($farm->vr) && $farm->vr)
                                    {{-- すでに登録済みの画像があれば表示（S3のURL等） --}}
                                    <p class="text-xs text-gray-500 mb-1">現在の登録画像:</p>
                                    <img src="{{ $farm->vr }}" alt="Current VR" class="w-full rounded shadow object-contain max-h-64">
                                @else
                                    {{-- 画像がない場合はNo Imageを表示 --}}
                                    <img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" class="w-full rounded shadow object-contain max-h-64 opacity-50">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="theme" class="leading-7 text-sm text-gray-600">ツアーテーマ</label>
                        <input type="text" id="theme" name="theme" value="{{ old('theme')}}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="prefecture" class="leading-7 text-sm text-gray-600">所在地：県名</label>
                        <input type="text" id="prefecture" name="prefecture" value="{{ old('prefecture')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="address" class="leading-7 text-sm text-gray-600">所在地：市町村以下</label>
                        <input type="text" id="address" name="address" value="{{ old('address')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                            <label for="hp_link" class="leading-7 text-sm text-gray-600">HPリンク</label>
                            <input type="url" id="hp_link" name="hp_link" value="{{ old('hp_link') }}" 
                                   class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 
                                   focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 
                                   text-base outline-none text-gray-700 py-1 px-3 leading-8 
                                   transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                            <label for="instagram_link" class="leading-7 text-sm text-gray-600">Instagram</label>
                            <input type="url" id="instagram_link" name="instagram_link" value="{{ old('instagram_link') }}" 
                                   class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 
                                   focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 
                                   text-base outline-none text-gray-700 py-1 px-3 leading-8 
                                   transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                            <label for="has_experience" class="leading-7 text-sm text-gray-600">体験可否</label>
                            <div class="flex items-center space-x-4">
                                <div>
                                    <input type="radio" id="experience_yes" name="has_experience" value="1" 
                                           {{ old('has_experience') == '1' ? 'checked' : '' }}>
                                    <label for="experience_yes">あり</label>
                                </div>
                                <div>
                                    <input type="radio" id="experience_no" name="has_experience" value="0" 
                                           {{ old('has_experience') == '0' ? 'checked' : '' }}>
                                    <label for="experience_no">なし</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
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
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function previewImage(input, previewId) {
            // VR画像用に10MBまで許可する設定に変更
            const maxFileSize = 10 * 1024 * 1024; 

            if (input.files && input.files[0]) {
                // ファイルサイズチェック
                if (input.files[0].size > maxFileSize) {
                    // 10MBを超える場合
                    alert('ファイルサイズは10MB以下にしてください。');
                    input.value = ''; // 選択されたファイルをクリア
                    
                    // プレビューをデフォルト画像（No Image）にリセット
                    // デザイン崩れを防ぐためクラスもTailwindに合わせる
                    document.getElementById(previewId).innerHTML = '<img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" class="w-full rounded shadow object-contain max-h-64 opacity-50">'; 
                    return; 
                }

                // ファイルサイズがOKの場合、画像をプレビュー
                var reader = new FileReader();
                reader.onload = function(e) {
                    // ここもTailwindのクラスを適用して、枠内にきれいに収まるように変更
                    document.getElementById(previewId).innerHTML = '<img src="' + e.target.result + '" alt="Image preview" class="w-full rounded shadow object-contain max-h-64">';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>  
</x-admin-layout>