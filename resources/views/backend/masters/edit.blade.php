<x-master-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $owner->name }}様
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('owner.backend.masters.update',['id' => $owner->farm->id]) }}" method="POST">
                @csrf
                @method('PUT')
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
                        <input type="text" id="name" name="name" value="{{ $farm->farm_name }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="catchcopy" class="leading-7 text-sm text-gray-600">キャッチコピー</label>
                        <input type="text" id="catchcopy" name="catchcopy" value="{{ $farm->catchcopy }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="kind" class="leading-7 text-sm text-gray-600">動物種類</label>
                        <div class="flex">
                            @foreach($kinds as $kind)
                                <div class='appearance'>
                                    <input type='checkbox' id='kind{{ $kind->id }}' name='kinds[]' value="{{ $kind->id }}" class='checkbox' {{ in_array($kind->id, $selected_kinds) ? 'checked' : '' }}>
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
                                    <input type='checkbox' id='kind{{ $keyword->id }}' name='keywords[]' value="{{ $keyword->id }}" class='checkbox' {{ in_array($keyword->id, $selected_keywords) ? 'checked' : '' }}>
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
                        <label for="vr" class="leading-7 text-sm text-gray-600">VR</label>
                        <input type="text" id="vr" name="vr" value="{{ $farm->vr }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="theme" class="leading-7 text-sm text-gray-600">ツアーテーマ</label>
                        <input type="text" id="theme" name="theme" value="{{ $farm->theme }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="prefecture" class="leading-7 text-sm text-gray-600">所在地：県名</label>
                        <input type="text" id="prefecture" name="prefecture" value="{{ $farm->prefecture }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="address" class="leading-7 text-sm text-gray-600">所在地：市町村以下</label>
                        <input type="text" id="address" name="address" value="{{ $farm->address }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-4 w-full mx-auto">
                        <div class="form-group">
                            <h1 style="text-align: center;">ご紹介文</h1>
                            <div class="col-md-12">        
                                <textarea name="editor1">{{ $farm->farm_info }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-4 w-full mx-auto">
                        <div class="relative flex justify-around">
                            <div>
                                <input type="radio" name="is_published" value="1" class="mr-2" @if(old('is_published', $farm->is_published) == 1) checked @endif>公開
                            </div>
                            <div>
                                <input type="radio" name="is_published" value="0" class="mr-2" @if(old('is_published', $farm->is_published) == 0) checked @endif>非公開
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">更新</button>
                </div>
            </form>
        </div>
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

</x-master-layout>