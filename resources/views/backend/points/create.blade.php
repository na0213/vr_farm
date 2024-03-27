<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    一覧
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                SDGsポイント登録
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.backend.points.store', ['farm' => $farm->id]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="-m-2">
                    <div class="p-2 w-1/2 mx-auto">
                        <div class="relative">
                        <label for="point_name" class="leading-7 text-sm text-gray-600">タイトル</label>
                        <input type="text" id="point_name" name="point_name" value="{{ old('point_name')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="point_info" class="leading-7 text-sm text-gray-600">SDGsポイント</label>
                        <textarea name="point_info" id="point_info" cols="30" rows="10" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"></textarea>
                        </div>
                    </div>
                </div>
                <div class="p-2 w-4/5 mx-auto">
                    <div class="relative">
                        <label class="leading-7 text-sm text-gray-600">ID</label>
                        <div class="mt-2">
                            @for ($i = 1; $i <= 17; $i++)
                                <div class="flex items-center mb-2">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="sdgs[]" value="{{ $i }}" class="form-checkbox" @if(is_array(old('sdgs')) && in_array($i, old('sdgs'))) checked @endif>
                                        <span class="ml-2">SDG{{ $i }}</span>
                                    </label>
                                    <img src="{{ asset('storage/sdgs/sdg_' . $i . '.png') }}" alt="SDG{{ $i }}" class="ml-4" width="50">
                                </div>
                            @endfor
                        </div>
                    </div>
                </div>
                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録</button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>