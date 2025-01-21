<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 flex justify-between items-center h-16">
        <!-- ロゴ -->
        <div class="flex items-center">
            <a href="{{ route('dashboard') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-black-800 dark:text-gray-200" />
            </a>
        </div>

        <!-- ナビゲーションリンク -->
        <div class="hidden space-x-8 lg:flex lg:items-center">
            <x-nav-link :href="route('user.farm.index')" :active="request()->routeIs('user.farm.index')">牧場検索</x-nav-link>
            <x-nav-link :href="route('contact.form')" :active="request()->routeIs('contact.form')">問い合わせ</x-nav-link>
            <x-nav-link :href="route('user.favorites')" :active="request()->routeIs('user.favorites')">お気に入り</x-nav-link>
            {{-- <x-nav-link :href="route('user.mypage.show')" :active="request()->routeIs('user.mypage.show')">マイページ</x-nav-link> --}}
        </div>

        <!-- ユーザーメニュー -->
        <div class="hidden sm:flex sm:items-center sm:ms-6">
            <x-dropdown align="right" width="48">
                <x-slot name="trigger">
                    <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                        <div>{{ Auth::user()->name }}</div>
                        <div class="ms-1">
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </button>
                </x-slot>
                <x-slot name="content">
                    <x-dropdown-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-dropdown-link>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-dropdown-link>
                    </form>
                </x-slot>
            </x-dropdown>
        </div>

        <!-- ハンバーガーメニュー -->
        <div class="-me-2 flex items-center lg:hidden">
            <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('user.farm.index')" :active="request()->routeIs('user.farm.index')">牧場検索</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contact.form')" :active="request()->routeIs('contact.form')">問い合わせ</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('user.favorites')" :active="request()->routeIs('user.favorites')">お気に入り</x-responsive-nav-link>
            {{-- <x-responsive-nav-link :href="route('user.mypage.show')" :active="request()->routeIs('user.mypage.show')">マイページ</x-responsive-nav-link> --}}
        </div>
        <div class="border-t border-gray-200 pt-4">
            <!-- Profile Link -->
            <x-responsive-nav-link :href="route('profile.edit')">{{ __('Profile') }}</x-responsive-nav-link>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="mt-1">
                @csrf
                <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                    this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>

