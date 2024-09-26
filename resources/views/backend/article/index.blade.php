<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            記事一覧
        </h2>
    </x-slot>

    <a href="{{ route('admin.backend.article.create') }}">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        新規登録
                    </div>
                </div>
            </div>
        </div>
    </a>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
          <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
              <tr>

                  <th scope="col" class="px-6 py-2 whitespace-nowrap">
                    ID
                  </th>
                  <th scope="col" class="px-6 py-2 whitespace-nowrap">
                    牧場</th>
                  <th scope="col" class="px-6 py-2 whitespace-nowrap">
                    タイトル</th>
                  <th scope="col" class="px-6 py-2 whitespace-nowrap">
                    編集
                  </th>
                  <th scope="col" class="px-6 py-2 whitespace-nowrap">
                    詳細
                  </th>
              </tr>
          </thead>
          <tbody>
            @forelse ($articles as $article)
              <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                  <td class="px-6 py-2">{{ $article->id }}</td>
                  <td class="px-6 py-2">{{ $article->farm->farm_name }}</td>
                  <td class="px-6 py-2">{{ $article->title }}</td>
                  <td class="px-6 py-2">
                      {{-- <a href="{{ route('admin.backend.article.edit', $article->id) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">編集</a> --}}
                  </td>
                  <td class="px-6 py-2">
                    {{-- <a href="{{ route('admin.backend.article.show', $article->id) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">詳細</a> --}}
                  </td>
              </tr>
            @empty
              <p class="p-5">まだ登録されていません。</p>
            @endforelse
          </tbody>
        </table>
    </div>
</x-admin-layout>