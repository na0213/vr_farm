<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.kinds.index') }}">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    一覧
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                種類登録
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.backend.kinds.store') }}" method="POST">
                @csrf
                <div class="-m-2">
                    <div class="p-2 w-1/2 mx-auto">
                        <div class="relative">
                        <label for="name" class="leading-7 text-sm text-gray-600">種類名</label>
                        <input type="text" id="name" name="name" value="{{ old('name')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
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