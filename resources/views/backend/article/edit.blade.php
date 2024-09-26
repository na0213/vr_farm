<x-admin-layout>
    <x-slot name="header">
        <div class="flex">
            <h2 class="pl-10 font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                オーナー編集
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-2 w-2/2">
                <div>
                  <label for="name" class="w-1/5 leading-7 text-sm text-gray-600">変更前</label>
                  <div class="flex w-3/5 bg-gray-100 pl-3 pt-2">
                    <p>名　前</p>
                    <p class="pl-10">{{ $owner->name }}</p>
                  </div>
                  <div class="flex w-3/5 bg-gray-100 pl-3 pt-2">
                    <p>メール</p>
                    <p class="pl-10">{{ $owner->email }}</p>
                  </div>
                </div>
            </div>

            <form action="{{ route('admin.backend.owners.update', $owner->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="p-2 w-2/2">
                    <div>
                        <label for="name" class="w-1/5 leading-7 text-sm text-gray-600">変更後</label>
                        <div class="flex w-3/5 bg-gray-100 pl-3 pt-2">
                            <p class="pr-10">名　前</p>
                            <input type="text" id="name" name="name" required class="w-3/5 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                        <div class="flex w-3/5 bg-gray-100 pl-3 pt-2">
                            <p class="pr-10">メール</p>
                            <input type="text" id="email" name="email" required class="w-3/5 bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                        </div>
                    </div>
                </div>
                <div class="p-2 w-full flex justify-around mt-4">
                    <button type="submit" onclick="return confirm('本当に更新しますか？');" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">更新</button>
                </div>
            </form>
            <div class="p-2 w-full flex justify-around mt-4">
                <form action="{{ route('admin.backend.owners.destroy', $owner->id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-white bg-red-500 border-0 py-2 px-8 focus:outline-none hover:bg-red-600 rounded text-lg" onclick="return confirm('本当に削除しますか？');">削除</button>
                </form>
            </div>
            <div class="p-2 w-full flex justify-around mt-4">
                <a href="{{ route('admin.backend.owners.index') }}">
                <button type="button" class="text-white bg-gray-500 border-0 py-2 px-8 focus:outline-none hover:bg-gray-600 rounded text-lg">戻る</button>
                </a>
            </div>
        </div>
    </div>

</x-admin-layout>