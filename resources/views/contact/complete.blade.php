<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <br>
送信しました。

<br>
<a href="{{ route('index') }}">トップページにもどる</a>
</x-guest-layout>
