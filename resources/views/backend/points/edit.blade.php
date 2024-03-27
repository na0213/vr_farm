<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    一覧
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                SDGsポイント編集
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.backend.points.update', ['id' => $point->id]) }}" method="POST" enctype="multipart/form-data">
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
                        <label for="point_name" class="leading-7 text-sm text-gray-600">タイトル</label>
                        <input type="text" id="point_name" name="point_name" value="{{ $point->point_name }}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>

                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="point_info" class="leading-7 text-sm text-gray-600">SDGsポイント</label>
                        <input type="text" id="point_info" name="point_info" value="{{ $point->point_info }}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>

                <div class="p-2 w-4/5 mx-auto">
                    <div class="relative">
                        <label class="leading-7 text-sm text-gray-600">SDGsポイント</label>
                        <div class="mt-2">
                            @php $sdgsArray = json_decode($point->sdgs, true); @endphp
                            @for ($i = 1; $i <= 17; $i++)
                                <label class="inline-flex items-center">
                                    <input type="checkbox" name="sdgs[]" value="{{ $i }}" class="form-checkbox" 
                                        {{ is_array($sdgsArray) && in_array($i, $sdgsArray) ? 'checked' : '' }}>
                                    <span class="ml-2">SDG{{ $i }}</span>
                                </label>
                            @endfor
                        </div>
                    </div>
                </div>                
                
                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">更新</button>
                </div>
            </form>
            <div class="p-2 w-full flex justify-around mt-4">
                <form action="{{ route('admin.backend.points.destroy', ['id' => $point->id]) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('本当に削除してよろしいですか？');" class="text-white bg-red-500 border-0 py-2 px-4 focus:outline-none hover:bg-red-600 rounded text-lg">削除</button>
                </form>
            </div>
        </div>
    </div>

</x-admin-layout>