<x-top-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />


    <div class="w-full">
        {!! $farm->vr !!}
    </div>


</x-top-layout>