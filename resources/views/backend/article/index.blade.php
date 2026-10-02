<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                記事一覧
            </h2>
            <a href="{{ route('admin.backend.article.create') }}" class="btn-butter text-sm">新規登録</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-600">
                    <thead class="text-xs text-gray-700 bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">状態</th>
                            <th scope="col" class="px-4 py-3">タイトル</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">牧場</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">更新日</th>
                            <th scope="col" class="px-4 py-3 whitespace-nowrap">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($articles as $article)
                            <tr class="border-t">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @if ($article->is_published)
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-800">公開中</span>
                                    @else
                                        <span class="inline-block px-2 py-0.5 rounded-full text-xs bg-gray-200 text-gray-600">下書き</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $article->title }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $article->farm->farm_name ?? 'コラム' }}</td>
                                <td class="px-4 py-3 whitespace-nowrap">{{ $article->updated_at?->format('Y/m/d') }}</td>
                                <td class="px-4 py-3 whitespace-nowrap space-x-3">
                                    <a href="{{ route('admin.backend.article.edit', $article->id) }}" class="underline">編集</a>
                                    <a href="{{ route('admin.backend.article.show', $article->id) }}" class="underline">詳細</a>
                                    @if ($article->is_published)
                                        <a href="{{ route('article.show', $article->id) }}" target="_blank" rel="noopener" class="underline">公開ページ</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500">まだ記事がありません。</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
