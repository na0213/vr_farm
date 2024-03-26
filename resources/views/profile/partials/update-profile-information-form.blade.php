<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="name" :value="__('prefecture')" />
            <x-text-input id="prefecture" name="prefecture" type="text" class="mt-1 block w-full" :value="old('prefecture', $user->prefecture)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('prefecture')" />
        </div>
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>
        {{-- Age --}}
        <div class="mt-4">
            <x-input-label for="age" :value="__('Age')" />
            <select id="age" name="age" class="block mt-1 w-full" required>
                <option value="">選択してください</option>
                @foreach(['10' => '10代', '20' => '20代', '30' => '30代', '40' => '40代', '50' => '50代', '60' => '60代', '70' => '70代以上'] as $value => $label)
                    <option value="{{ $value }}" @if(old('age', $user->age) == $value) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('age')" class="mt-2" />
        </div>

        {{-- Gender --}}
        <div class="mt-4">
            <x-input-label :value="__('Gender')" />
            <div class="mt-1">
                <label for="gender_female" class="inline-flex items-center">
                    <input id="gender_female" type="radio" name="gender" value="female" @if(old('gender', $user->gender) == 'female') checked @endif class="mr-2" />{{ __('Female') }}
                </label>
                <label for="gender_male" class="inline-flex items-center ml-6">
                    <input id="gender_male" type="radio" name="gender" value="male" @if(old('gender', $user->gender) == 'male') checked @endif class="mr-2" />{{ __('Male') }}
                </label>
                <label for="gender_unspecified" class="inline-flex items-center ml-6">
                    <input id="gender_unspecified" type="radio" name="gender" value="unspecified" @if(old('gender', $user->gender) == 'unspecified') checked @endif class="mr-2" />{{ __('Unspecified') }}
                </label>
            </div>
            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
