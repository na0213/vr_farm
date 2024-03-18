<x-master-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $owner->name }}様
        </h2>
    </x-slot>

    {{-- <a href="{{ route('master.backend.owners.index') }}"> --}}
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex justify-around">
                <a href="{{ route('owner.backend.masters.edit', ['id' => $owner->farm->id]) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">
                    <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">編集</button>
                </a>

                <a href="{{ route('owner.backend.masters.show', ['id' => $owner->farm->id]) }}" class="px-3 py-2 text-black bg-detail text-md hover:bg-yellow-500">
                    <button type="submit" class="text-white bg-green-500 border-0 py-2 px-8 focus:outline-none hover:bg-green-600 rounded text-lg">プレビュー</button>
                </a>
            </div>
        </div>
    </div>
    {{-- </a> --}}
</x-master-layout>