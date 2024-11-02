<nav x-data="{ open: false }" class="bg-[#F2EEEA]">
    <!-- Primary Navigation Menu -->
    <div class="mx-auto px-4 flex justify-between items-center h-16">
        <!-- ロゴ -->
        <div class="flex items-center">
            <a href="{{ route('index') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-black-800 dark:text-gray-200" />
            </a>
        </div>

        <!-- ナビゲーションリンク -->
        <div class="hidden space-x-8 lg:flex lg:items-center"> <!-- sm:flex を lg:flex に変更 -->
            <x-nav-link :href="route('login')" :active="request()->routeIs('login')">ログイン</x-nav-link>
            <x-nav-link :href="route('register')" :active="request()->routeIs('register')">新規登録</x-nav-link>
            <x-nav-link :href="route('contact.form')" :active="request()->routeIs('contact.form')">お問い合わせ</x-nav-link>
        </div>

        <!-- ハンバーガーメニュー -->
        <div class="-me-2 flex items-center lg:hidden"> <!-- lg:hidden を維持 -->
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
            <x-responsive-nav-link :href="route('shop.login')" :active="request()->routeIs('shop.login')">
                ログイン
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                新規登録
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('contact.form')" :active="request()->routeIs('contact.form')">
                お問い合わせ
            </x-responsive-nav-link>
        </div>
    </div>
</nav>
