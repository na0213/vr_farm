<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
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
                    <p>オーナー</p>
                    <p class="pl-10">{{ $owner->name }}様</p>
                  </div>
                  <div class="flex w-3/5 bg-gray-100 pl-3 pt-2">
                    <p>メール　</p>
                    <p class="pl-10">{{ $owner->email }}</p>
                  </div>
                </div>
            </div>

            {{-- 牧場 --}}
            <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
              <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          ID
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          牧場名
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          情報編集
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          画像管理
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          プレビュー
                        </th>
                    </tr>
                </thead>
                <tbody>
                  @if($farm)
                    <div class="flex justify-between">
                      <p class="p-5">【牧場管理】</p>
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
                        <a href="{{ route('admin.admin.backend.farms.editImages', ['farmId' => $farm->id]) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">編集</a>
                      </td>
                      <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                        <a href="{{ route('admin.backend.farms.show', ['id' => $farm->id]) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">詳細</a>
                      </td>
                    </tr>
                  @else
                    <div class="p-2 w-full flex justify-around mt-4">
                      <a href="{{ route('admin.backend.farms.create', ['owner' => $owner->id]) }}">
                        <button type="button" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">牧場登録</button>
                        </a>
                    </div>
                  @endif
                </tbody>
              </table>
            </div>
            {{-- 飼育 --}}
            <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
              <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          ID
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          タイトル
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          編集
                        </th>
                    </tr>
                </thead>
                <tbody>
                  @if($farm)
                  <div class="flex justify-between">
                    <p class="p-5">【飼育管理】</p>
                    <div class="m-3">
                      <a href="{{ route('admin.backend.animals.create', ['farm' => $farm->id]) }}">
                        <button type="button" class="text-white bg-yellow-500 border-0 py-1 px-4 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録</button>
                      </a>
                    </div>
                  </div>
                  @endif
                  @if ($farm && $farm->animals->count() > 0)
                    @foreach ($farm->animals as $animal)
                      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $animal->id }}
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $animal->animal_name }}
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                          <a href="{{ route('admin.backend.animals.edit', ['id' => $animal->id]) }}">編集</a>
                        </td>
                      </tr>
                    @endforeach
                  @else
                  <tr>
                    <td colspan="3" class="text-center py-4">
                        @if ($farm)
                            <a href="{{ route('admin.backend.animals.create', ['farm' => $farm->id]) }}">新規登録</a>
                        @else
                            飼育管理を登録してください。
                        @endif
                    </td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
            {{-- 商品 --}}
            <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
              <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          ID
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          商品名
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          編集
                        </th>
                    </tr>
                </thead>
                <tbody>
                  @if($farm)
                  <div class="flex justify-between">
                    <p class="p-5">【商品管理】</p>
                    <div class="m-3">
                      <a href="{{ route('admin.backend.products.create', ['farm' => $farm->id]) }}">
                        <button type="button" class="text-white bg-yellow-500 border-0 py-1 px-4 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録</button>
                      </a>
                    </div>
                  </div>
                  @endif
                  @if ($farm && $farm->products->count() > 0)
                    @foreach ($farm->products as $product)
                      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->id }}
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $product->product_name }}
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                          <a href="{{ route('admin.backend.products.edit', ['id' => $product->id]) }}">編集</a>
                        </td>
                      </tr>
                    @endforeach
                  @else
                  <tr>
                    <td colspan="3" class="text-center py-4">
                      商品を登録してください。
                    </td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
            {{-- 販売店 --}}
            <div class="relative mt-5 overflow-x-auto shadow-md sm:rounded-lg">
              <table class="w-11/12 mx-auto mb-10 text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          ID
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          販売店
                        </th>
                        <th scope="col" class="px-6 py-2 whitespace-nowrap">
                          編集
                        </th>
                    </tr>
                </thead>
                <tbody>
                  @if($farm)
                  <div class="flex justify-between">
                    <p class="p-5">【販売店管理】</p>
                    <div class="m-3">
                      <a href="{{ route('admin.backend.stores.create', ['farm' => $farm->id]) }}">
                        <button type="button" class="text-white bg-yellow-500 border-0 py-1 px-4 focus:outline-none hover:bg-yellow-600 rounded text-lg">登録</button>
                      </a>
                    </div>
                  </div>
                  @endif
                  @if ($farm && $farm->stores->count() > 0)
                    @foreach ($farm->stores as $store)
                      <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $store->id }}
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                            {{ $store->store_name }}
                        </td>
                        <td scope="row" class="px-6 py-2 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                          <a href="{{ route('admin.backend.stores.edit', ['id' => $store->id]) }}">編集</a>
                        </td>
                      </tr>
                    @endforeach
                  @else
                  <tr>
                    <td colspan="3" class="text-center py-4">
                      販売店を登録してください。
                    </td>
                  </tr>
                  @endif
                </tbody>
              </table>
            </div>
            <div class="p-2 w-full flex justify-around mt-4">
              <a href="{{ route('admin.backend.owners.index') }}">
              <button type="button" class="text-white bg-gray-500 border-0 py-2 px-8 focus:outline-none hover:bg-gray-600 rounded text-lg">戻る</button>
              </a>
          </div>
        </div>
    </div>

</x-admin-layout>