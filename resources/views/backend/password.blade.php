<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            パスワード変更
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                @if (session('status') === 'password-updated')
                    <p class="mb-4 text-sm text-green-700">パスワードを変更しました。</p>
                @endif

                <form method="POST" action="{{ route('admin.password.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- パスワードマネージャーが、どのログインの更新かを判別するための欄(画面には出さない) --}}
                    <input type="text" name="username" value="{{ Auth::user()->email }}" autocomplete="username" readonly tabindex="-1" aria-hidden="true" class="sr-only">

                    <div>
                        <x-input-label for="current_password" value="現在のパスワード" />
                        <x-text-input id="current_password" name="current_password" type="password" class="block mt-1 w-full" autocomplete="current-password" required />
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" value="新しいパスワード(8文字以上)" />
                        <x-text-input id="password" name="password" type="password" class="block mt-1 w-full" autocomplete="new-password" minlength="8" required />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password_confirmation" value="新しいパスワード(確認)" />
                        <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full" autocomplete="new-password" required />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>変更する</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout>
