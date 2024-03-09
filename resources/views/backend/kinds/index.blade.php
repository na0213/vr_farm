<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            カテゴリー（種類）一覧
        </h2>
    </x-slot>

    <a href="{{ route('admin.backend.kinds.create') }}">
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
                    種類</th>
                  <th scope="col" class="px-6 py-2 whitespace-nowrap">
                    編集
                  </th>
              </tr>
          </thead>
          <tbody>
            @forelse ($kinds as $kind)
              <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                  <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $kind->id }}
                  </td>
                  <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    {{ $kind->kind }}
                  </td>
                  <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                      <a href="{{ route('admin.backend.kinds.edit', $kind->id) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">編集</a>
                  </td>
              </tr>
              @empty
              <p class="p-5">まだ登録されていません。</p>
          @endforelse
          </tbody>
        </table>
    </div>
</x-admin-layout>