<x-adminfarm-layout>
  <x-slot name="header">
    <div class="flex">
        <a href="{{ route('admin.backend.owners.show', $owner->id) }}">
            <h2 class="text-xl text-gray-600 dark:text-gray-200 leading-tight">
                オーナー詳細
            </h2>
        </a>
        <a href="{{ route('admin.backend.farms.edit', $farm->id) }}">
          <h2 class="pl-10 text-xl text-gray-900 dark:text-gray-200 leading-tight">
              牧場登録
          </h2>
        </a>
        <a href="{{ route('admin.backend.farms.images', ['id' => $farm->id]) }}">
          <h2 class="pl-10 text-xl text-gray-600 dark:text-gray-200 leading-tight">
              画像登録
          </h2>
        </a>
        <a href="{{ route('admin.backend.farms.show', ['id' => $farm->id]) }}">
            <h2 class="pl-10 text-xl text-gray-600 dark:text-gray-200 leading-tight">
                プレビュー
            </h2>
        </a>
    </div>
  </x-slot>

    <div class="rtop-title mt-20">
      <h1 class="rtop-content">
        <span class="rfarm-name">{{ $farm->farm_name }}</span>
        <span class="rfarm-address">{{ $farm->prefecture }}</span>
      </h1>
    </div>

    <div class="w-full mt-20">
        {!! $farm->vr !!}
    </div>

    <div class="farm-info m-5 sm:m-20">
        {!! $farm->farm_info !!}
    </div>


</x-adminfarm-layout>