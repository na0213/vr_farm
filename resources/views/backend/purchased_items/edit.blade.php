<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-baseline gap-6">
            <a href="{{ route('admin.backend.purchased-items.create', ['farm' => $item->farm_id]) }}" class="text-gray-600 underline">一覧へ戻る</a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                購入した商品の編集 — {{ $item->farm->farm_name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('admin.backend.purchased-items.update', $item->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('backend.purchased_items.fields', ['item' => $item])
                    <button type="submit" class="mt-6 text-white bg-yellow-500 border-0 py-2 px-8 hover:bg-yellow-600 rounded text-lg">更新</button>
                </form>
            </div>

            <form action="{{ route('admin.backend.purchased-items.destroy', $item->id) }}" method="POST" class="text-right">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm(@js('「'.$item->item_name.'」を削除します。写真も消えます。よろしいですか?'));"
                        class="text-white bg-red-500 border-0 py-2 px-4 hover:bg-red-600 rounded">削除</button>
            </form>
        </div>
    </div>
</x-admin-layout>
