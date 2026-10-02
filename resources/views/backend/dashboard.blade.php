<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            管理画面
        </h2>
    </x-slot>

    @php
        $menus = [
            ['route' => 'admin.backend.farms.index', 'title' => '牧場一覧', 'text' => '牧場・動物・商品・販売店の登録と編集'],
            ['route' => 'admin.backend.article.index', 'title' => '記事', 'text' => '読みものの登録・編集・公開'],
            ['route' => 'admin.backend.owners.index', 'title' => 'オーナー管理', 'text' => '牧場の新規登録はここから'],
            ['route' => 'admin.backend.kinds.index', 'title' => '種類', 'text' => '牛・豚・鶏などのカテゴリー'],
            ['route' => 'admin.backend.keywords.index', 'title' => 'キーワード', 'text' => '放牧・平飼いなどの特徴タグ'],
            ['route' => 'admin.password.edit', 'title' => 'パスワード変更', 'text' => 'ログイン用のパスワード'],
        ];
    @endphp

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-stretch grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($menus as $menu)
                    <a href="{{ route($menu['route']) }}" class="block bg-white rounded-xl shadow-sm hover:shadow-md transition p-6">
                        <p class="text-lg font-bold text-gray-800">{{ $menu['title'] }}</p>
                        <p class="mt-1 text-sm text-gray-500">{{ $menu['text'] }}</p>
                    </a>
                @endforeach
            </div>
            <p class="mt-8 text-sm">
                <a href="{{ route('index') }}" target="_blank" rel="noopener" class="underline text-gray-600">公開サイトを見る</a>
            </p>
        </div>
    </div>
</x-admin-layout>
