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
        {{-- {!! $farm->vr !!} --}}
    </div>

    <div class="flex items-center justify-center">
        <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
            <div>Story</div>
            <div class="cp_box">
                <input id="cp01" type="checkbox">
                <label for="cp01"></label>
                <div class="cp_container">
                    <p>{!! $farm->farm_info !!}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="flex items-center justify-center">
        <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
            <div>Raise</div>
            <div>
                <p>何を食べて育っているの？<br>
                どんな環境で育っているの？</p>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-center">
        <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
            <div>Store</div>
            <div>
                <p>商品・販売店一覧</p>
            </div>
        </div>
    </div>

    {{-- <div class="flex items-center justify-center">
        <div class="w-4/5 mt-20 p-5 roundedoverflow-hidden shadow-lg">
            {!! $farm->farm_info !!}
        </div>
    </div> --}}

</x-farm-layout>