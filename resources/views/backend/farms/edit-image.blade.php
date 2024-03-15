<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
                <h2 class="text-xl text-gray-600 dark:text-gray-200 leading-tight">
                    オーナー詳細
                </h2>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach ($farm->farmImages as $index => $image)
            <form action="{{ route('admin.backend.farms.updateImage', ['farmId' => $farm->id, 'imageId' => $image->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('POST')
                <div class="flex-container mb-4">
                    <div class="image-input">
                        <label for="upload_image{{ $index + 1 }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">画像 {{ $index + 1 }}<span class="text-red-600">（1MB以下）</span></label>
                        <input type="file" name="image" id="upload_image{{ $index + 1 }}" accept="image/*" onchange="previewImage(this, 'preview_image{{ $index + 1 }}')" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        <div class="image-preview mt-2" id="preview_image{{ $index + 1 }}">
                            <img src="{{ $image->image_path }}" alt="Image preview" style="width: 200px; height: auto;">
                        </div>
                    </div>
                </div>
                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">更新</button>
                </div>
            </form>
            @endforeach

            {{-- ここから新規登録 --}}
            <div class="mt-10">
                @php
                $existingImagesCount = $farm->farmImages->count();
                $maxImages = 4;
                @endphp
            
                @if ($existingImagesCount < $maxImages)
                <form action="{{ route('admin.backend.farms.storeImages', ['id' => $farm->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
            
                    @for ($i = 0; $i < $maxImages - $existingImagesCount; $i++)
                    <div class="flex-container mb-4">
                        <div class="image-input">
                            <label for="new_image{{ $i }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">画像 {{ $existingImagesCount + $i + 1 }}<span class="text-red-600">（1MB以下）</span></label>
                            <input type="file" name="images[]" id="new_image{{ $i }}" accept="image/*" onchange="previewImage(this, 'preview_image{{ $i }}')" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                            <div class="image-preview mt-2" id="preview_image{{ $i }}">
                                <img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" class="image-preview">
                            </div>
                        </div>
                    </div>
                    @endfor
            
                    <div class="p-2 w-full flex justify-around mt-4">
                        <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録</button>
                    </div>
                </form>
                @endif
            </div>
        </div>
    </div>

    <script>
            function previewImage(input, previewId) {
        const maxFileSize = 1 * 1024 * 1024; // 1MBをバイト単位で定義

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
</x-admin-layout>