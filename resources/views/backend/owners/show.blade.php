<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <a href="{{ route('admin.backend.owners.index') }}">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    一覧
                </h2>
            </a>
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                オーナー詳細
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-2 w-2/2">
                <div>
                  <div class="flex w-3/5 bg-gray-100 pl-3 pt-2">
                    <p>名　前</p>
                    <p class="pl-10">{{ $owner->name }}様</p>
                  </div>
                  <div class="flex w-3/5 bg-gray-100 pl-3 pt-2">
                    <p>メール</p>
                    <p class="pl-10">{{ $owner->email }}</p>
                  </div>
                </div>
            </div>

            <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <div class="p-2">牧場管理</div>
                      <tr>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            ID
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            牧場名</th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            編集
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            詳細
                          </th>
                      </tr>
                  </thead>
                  <tbody>
                    @if($farm)
                      <div class="flex justify-between">
                        <p class="p-5">牧場</p>
                        <a href="{{ route('admin.backend.farms.create', ['owner' => $owner->id]) }}">
                            <p class="p-5">新規登録</p>
                        </a>
                        <a href="{{ route('admin.backend.farms.images', ['id' => $farm->id]) }}">
                          <p class="p-5">画像登録</p>
                        </a>
                      </div>
                      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $farm->id }}
                          </td>
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $farm->farm_name }}
                          </td>
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                              <a href="{{ route('admin.backend.farms.edit', $farm->id) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">編集</a>
                          </td>
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            <a href="{{ route('admin.backend.farms.show', ['id' => $farm->id]) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">詳細</a>
                          </td>
                      </tr>
                      @else
                      <div class="flex justify-between">
                        <p class="p-5">まだ登録されていません。</p>
                        <a href="{{ route('admin.backend.farms.create', ['owner' => $owner->id]) }}">
                            <p class="p-5">新規登録</p>
                        </a>
                      </div>
                    @endif
                  </tbody>
                </table>
            </div>

            <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <div class="p-2">飼育管理</div>
                      <tr>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            情報編集
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            画像編集
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            詳細
                          </th>
                      </tr>
                  </thead>
                  <tbody>
                      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        </td>
                      </tr>
                      <div class="flex justify-between">
                        <p class="p-5">まだ登録されていません。</p>
                        <p class="p-5">新規登録</p>
                        <p class="p-5">画像登録</p>
                      </div>
                  </tbody>
                </table>
            </div>

            <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                  <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <div class="p-2">店舗・商品管理</div>
                      <tr>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            ID
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            商品名</th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            編集
                          </th>
                          <th scope="col" class="px-6 py-2 whitespace-nowrap">
                            詳細
                          </th>
                      </tr>
                  </thead>
                  <tbody>
                    {{-- @forelse ($owners as $owner) --}}
                      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{-- {{ $owner->id }} --}}
                          </td>
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{-- {{ $owner->name }} --}}
                          </td>
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                              {{-- <a href="{{ route('admin.backend.owners.edit', $owner->id) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">編集</a> --}}
                          </td>
                          <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{-- <a href="{{ route('admin.backend.owners.show', $owner->id) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">詳細</a> --}}
                        </td>
                      </tr>
                      {{-- @empty --}}
                      <div class="flex justify-between">
                        <p class="p-5">まだ登録されていません。</p>
                        <p class="p-5">新規登録</p>
                      </div>
                  {{-- @endforelse --}}
                  </tbody>
                </table>
            </div>
        </div>
    </div>

</x-admin-layout>