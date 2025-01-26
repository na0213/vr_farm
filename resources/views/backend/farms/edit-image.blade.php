<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
                <h2 class="text-xl text-gray-600 dark:text-gray-200 leading-tight">
                    戻る
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                画像編集
            </h2>
        </div>
    </x-slot>

    <div class="m-5 py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @foreach ($farm->farmImages->sortBy('image_order') as $index => $image)
            <form action="{{ route('admin.backend.farms.updateImage', ['farmId' => $farm->id, 'imageId' => $image->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="flex-container mb-4">
                    <div class="image-input">
                        <label for="upload_image{{ $image->id }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">
                            画像 {{ $image->image_order }}
                        </label>
                        <input type="file" name="image" id="upload_image{{ $image->id }}" accept="image/*" 
                            onchange="previewImage(this, 'preview_image{{ $image->id }}')" 
                            class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        <div class="image-preview mt-2" id="preview_image{{ $image->id }}">
                            <img src="{{ $image->image_path }}" alt="Image preview" style="width: 200px; height: auto;">
                        </div>
                    </div>
                    <div class="p-2 w-full flex justify-around mt-4">
                        <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">
                            更新
                        </button>
                    </div>
                </div>
            </form>
                {{-- 削除ボタン --}}
                <div class="p-2 w-full flex justify-around mt-4">
                    <form action="{{ route('admin.backend.farms.deleteImage', ['farmId' => $farm->id, 'imageId' => $image->id]) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('本当に削除してよろしいですか？');" class="text-white bg-red-500 border-0 py-2 px-4 focus:outline-none hover:bg-red-600 rounded text-lg">削除</button>
                    </form>
                </div>
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
                            <label for="new_image{{ $i }}" class="block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300">画像 {{ $existingImagesCount + $i + 1 }}<span class="text-red-600">（3MB以下）</span></label>
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
    const maxFileSize = 3 * 1024 * 1024; // 3MBの制限

    if (input.files && input.files[0]) {
        // ファイルサイズチェック
        if (input.files[0].size > maxFileSize) {
            alert('ファイルサイズは3MB以下にしてください。');
            input.value = ''; // ファイル選択をリセット
            document.getElementById(previewId).innerHTML = '<img src="{{ asset('storage/noimage.jpg') }}" alt="No Image" style="width: 200px; height: auto;">';
            return;
        }

        // プレビュー画像を設定
        const reader = new FileReader();
        reader.onload = function (e) {
            const previewElement = document.getElementById(previewId);
            if (previewElement) {
                previewElement.innerHTML = '<img src="' + e.target.result + '" alt="Image preview" style="width: 200px; height: auto;">';
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
</x-admin-layout>