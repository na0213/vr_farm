<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.farms.index') }}">
                <h2 class="text-xl text-gray-600 dark:text-gray-200 leading-tight">
                    戻る
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                販売店 — {{ $farm->farm_name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- 登録済みの販売店 --}}
            <div class="w-4/5 mx-auto mb-10 space-y-4">
                @if (session('success') || session('message'))
                    <p class="rounded border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') ?? session('message') }}</p>
                @endif

                <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-600">
                        <thead class="text-xs text-gray-700 bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">販売店名</th>
                                <th scope="col" class="px-4 py-3">住所</th>
                                <th scope="col" class="px-4 py-3 whitespace-nowrap">リンク</th>
                                <th scope="col" class="px-4 py-3">管理</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($farm->stores as $row)
                                <tr class="border-t">
                                    <td class="px-4 py-2 font-medium text-gray-900">{{ $row->store_name }}</td>
                                    <td class="px-4 py-2">{{ $row->store_address ?: 'ー' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $row->store_link ? 'あり' : 'ー' }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">
                                        <a href="{{ route('admin.backend.stores.edit', $row->id) }}" class="underline">編集</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">まだ登録がありません。</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <h3 class="w-4/5 mx-auto font-semibold text-gray-800 mb-2">新しく登録する</h3>
            <form action="{{ route('admin.backend.stores.store', ['farm' => $farm->id]) }}" method="POST" enctype="multipart/form-data">
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
                        <label for="store_name" class="leading-7 text-sm text-gray-600">販売店名</label>
                        <input type="text" id="store_name" name="store_name" value="{{ old('store_name')}}" required class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>

                <div class="-m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="store_address" class="leading-7 text-sm text-gray-600">住所</label>
                        <input type="text" id="store_address" name="store_address" value="{{ old('store_address')}}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                
                <div class="m-2">
                    <div class="p-2 w-4/5 mx-auto">
                        <div class="relative">
                        <label for="store_link" class="leading-7 text-sm text-gray-600">リンク</label>
                        <input type="text" id="store_link" name="store_link" value="{{ old('store_link')}}" class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
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