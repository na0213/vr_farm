<x-farm-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="top-title">
        <h1 class="top-content">
          <span class="farm-name">{{ $farm->farm_name }}</span>
          <span class="farm-address">{{ $farm->prefecture }}</span>
        </h1>
    </div>

    <div class="w-full mt-20">
        {!! $farm->vr !!}
    </div>

    <div class="farm-info m-5 sm:m-20">
        {!! $farm->farm_info !!}
    </div>

</x-farm-layout>