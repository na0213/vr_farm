<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            牧場一覧
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <p class="mb-4 rounded border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</p>
            @endif

            <div class="mb-4 px-4 sm:px-0">
                <a href="{{ route('admin.backend.farms.create') }}" class="inline-block text-white bg-yellow-500 hover:bg-yellow-600 rounded py-2 px-6">牧場を登録</a>
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">牧場名</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">都道府県</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">公開</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($farms as $farm)
                            <tr class="border-t">
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">{{ $farm->farm_name }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $farm->prefecture }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($farm->is_published)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">公開中</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-200 text-gray-600">非公開</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap space-x-3">
                                    <a href="{{ route('admin.backend.farms.edit', $farm->id) }}" class="underline">編集</a>
                                    <a href="{{ route('admin.admin.backend.farms.editImages', ['farmId' => $farm->id]) }}" class="underline">画像</a>
                                    <a href="{{ route('admin.backend.animals.create', ['farm' => $farm->id]) }}" class="underline">動物({{ $farm->animals_count }})</a>
                                    <a href="{{ route('admin.backend.products.create', ['farm' => $farm->id]) }}" class="underline">撮影一覧({{ $farm->products_count }})</a>
                                    <a href="{{ route('admin.backend.purchased-items.create', ['farm' => $farm->id]) }}" class="underline">購入した商品({{ $farm->purchased_items_count }})</a>
                                    <a href="{{ route('admin.backend.stores.create', ['farm' => $farm->id]) }}" class="underline">販売店({{ $farm->stores_count }})</a>
                                    @if ($farm->is_published)
                                        <a href="{{ route('farm.show', $farm->id) }}" target="_blank" rel="noopener" class="underline">公開ページ</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">まだ牧場が登録されていません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
