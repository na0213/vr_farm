<x-master-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            更新
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="post" action="{{ route('owner.backend.masters.profile.update') }}" class="mt-6 space-y-6">
            @csrf
            @method('patch')

            <div>
                <x-input-label for="name" :value="__('Name')" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" value="{{ $master->name }}" required autofocus />
            </div>

            <div>
                <x-input-label for="email" :value="__('Email')" />
                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" value="{{ $master->email }}" required />
            </div>

            <div>
                <x-input-label for="password" :value="__('New Password')" />
                <p>（8文字以上）</p>
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
            </div>

            <div>
                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
            </div>

            <div class="p-2 w-full flex justify-around mt-4">
                <button type="submit" class="text-white bg-yellow-500 border-0 py-2 px-8 focus:outline-none hover:bg-yellow-600 rounded text-lg">更新</button>
            </div>
            {{-- <div class="flex items-center justify-between">
                <x-primary-button>{{ __('Update') }}</x-primary-button>
            </div> --}}
        </form>
        </div>
    </div>
</x-master-layout>