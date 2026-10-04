<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-6">
            <a href="{{ route('admin.backend.farms.index') }}" class="text-gray-600 underline">牧場一覧へ戻る</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                購入した商品 — {{ $farm->farm_name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            <p class="text-sm text-gray-600">
                自分で買って撮影した商品を登録します。牧場ページの「買ってみた」と、お取り寄せページに出ます(登録した順)。
                本番に出すには、いつもどおり書き出しと反映が必要です。
            </p>

            @if (session('success'))
                <p class="rounded border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('success') }}</p>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3">写真</th>
                            <th scope="col" class="px-4 py-3">商品名</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">販売ページ</th>
                            <th scope="col" class="px-4 py-3">管理</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($farm->purchasedItems as $row)
                            <tr class="border-t">
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <img src="{{ $row->item_image }}" alt="" class="inline-block w-16 h-16 object-cover rounded">
                                    <span class="ml-1 text-xs text-gray-500">{{ count($row->images()) }}枚</span>
                                </td>
                                <td class="px-4 py-2 font-medium text-gray-900">{{ $row->item_name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $row->item_link ? 'あり' : 'ー' }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">
                                    <a href="{{ route('admin.backend.purchased-items.edit', $row->id) }}" class="underline">編集</a>
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

            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="font-semibold text-gray-800 mb-4">新しく登録する</h3>
                <form action="{{ route('admin.backend.purchased-items.store', ['farm' => $farm->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @include('backend.purchased_items.fields', ['item' => null])
                    <button type="submit" class="mt-6 text-white bg-yellow-500 border-0 py-2 px-8 hover:bg-yellow-600 rounded text-lg">登録</button>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
