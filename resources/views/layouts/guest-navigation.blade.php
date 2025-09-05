<nav class="header-nav" x-data="{ open: false }" role="navigation" aria-label="グローバルナビゲーション">
    <!-- Primary Navigation Menu -->
    <div class="header-inner mx-auto px-4 flex justify-between items-center h-16">
        <!-- ロゴ -->
        <div class="flex items-center">
            <a href="{{ route('index') }}">
                <x-application-logo class="block h-9 w-auto fill-current text-black-800 dark:text-gray-200" />
            </a>
        </div>

        <!-- ナビゲーションリンク -->
        <div class="primary-links hidden space-x-3 lg:flex lg:items-center">
            <x-nav-link class="nav-pill" :href="route('farm.index') " :active="request()->routeIs('farm.index')">牧場検索</x-nav-link>
            <x-nav-link class="nav-pill" :href="route('contact.form')" :active="request()->routeIs('contact.form')">問い合わせ</x-nav-link>
            {{-- <x-nav-link :href="route('login')" :active="request()->routeIs('login')">ログイン</x-nav-link>
            <x-nav-link :href="route('register')" :active="request()->routeIs('register')">新規登録</x-nav-link> --}}
        </div>

        <!-- ハンバーガーメニュー -->
        <div class="-me-2 flex items-center lg:hidden nav-toggle">
            <button @click="open = ! open" :aria-expanded="open.toString()" aria-controls="mobile-menu" aria-label="メニューを開閉" class="inline-flex items-center justify-center p-2 rounded-md focus:outline-none transition duration-150 ease-in-out">
                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" id="mobile-menu" class="hidden lg:hidden">
        <div class="pt-2 pb-3 space-y-1 mobile-sheet">
            <x-responsive-nav-link class="nav-sheet-link" :href="route('farm.index')" :active="request()->routeIs('farm.index')">
                牧場検索
            </x-responsive-nav-link>
            <x-responsive-nav-link class="nav-sheet-link" :href="route('contact.form')" :active="request()->routeIs('contact.form')">
                問い合わせ
            </x-responsive-nav-link>
            {{-- <x-responsive-nav-link :href="route('login')" :active="request()->routeIs('login')">
                ログイン
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('register')" :active="request()->routeIs('register')">
                新規登録
            </x-responsive-nav-link> --}}
        </div>
    </div>
</nav>
