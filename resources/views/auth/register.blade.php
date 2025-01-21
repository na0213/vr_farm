<x-top-layout>
    <form class="custom-form" method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" 
            type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!--prefecture -->
        <div class="mt-4">
            <x-input-label for="prefecture" :value="__('prefecture')" />
            <x-text-input id="prefecture" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" 
            type="text" name="prefecture" :value="old('prefecture')" required autofocus autocomplete="prefecture" />
            <x-input-error :messages="$errors->get('prefecture')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" 
            type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        {{-- age --}}
        <div class="mt-4">
            <x-input-label for="age" :value="__('Age')" />
            <select id="age" name="age" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" required>
                <option value="">選択してください</option>
                @foreach(['10' => '10代', '20' => '20代', '30' => '30代', '40' => '40代', '50' => '50代', '60' => '60代', '70' => '70代以上'] as $value => $label)
                    <option value="{{ $value }}" @if(old('age') == $value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('age')" class="mt-2" />
        </div>

        {{-- Gender --}}
        <div class="mt-4">
            <x-input-label :value="__('Gender')" />
            <div class="mt-1">
                <label for="gender_female" class="inline-flex items-center">
                    <input id="gender_female" type="radio" name="gender" value="female" @if(old('gender') == 'female') checked @endif class="mr-2" />{{ __('Female') }}
                </label>
                <label for="gender_male" class="inline-flex items-center ml-6">
                    <input id="gender_male" type="radio" name="gender" value="male" @if(old('gender') == 'male') checked @endif class="mr-2" />{{ __('Male') }}
                </label>
                <label for="gender_unspecified" class="inline-flex items-center ml-6">
                    <input id="gender_unspecified" type="radio" name="gender" value="unspecified" @if(old('gender') == 'unspecified') checked @endif class="mr-2" />{{ __('Unspecified') }}
                </label>
            </div>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>
        
        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-top-layout>
