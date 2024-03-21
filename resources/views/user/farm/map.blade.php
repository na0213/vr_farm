<x-app-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    {{-- <div class="flex"> --}}
        <div class="wrap">
            <ul class="grid ">
                @foreach ($farms as $farm)
                    <li class="card rounded overflow-hidden shadow-lg">
                    <a href="{{ route('user.farm.show', ['id' => $farm->id]) }}">
                        @if($farm->farmImages->isNotEmpty())
                            <figure class="farmimg">
                                <img src="{{ $farm->farmImages->first()->image_path }}">
                            </figure>
                        @else
                            <figure class="farmimg">
                                <img src="../storage/noimage.jpg">
                            </figure>
                        @endif
                        <p class="ttl">{{ $farm->farm_name }}</p>
                        <p class="icon">
                            @foreach ($farm->kinds as $kind)
                            @switch($kind->id)
                                @case(1)
                                    <img src="../storage/cow.PNG" alt="Cow">
                                    @break
                                @case(2)
                                    <img src="../storage/ushi.png" alt="Ushi">
                                    @break
                                @case(3)
                                    <img src="../storage/bird.PNG" alt="Bird">
                                    @break
                                @case(4)
                                    <img src="../storage/pig.PNG" alt="Pig">
                                    @break
                            @endswitch
                            @endforeach
                        </p>
                        @if (!empty($farm->catchcopy))
                            <p class="txt">{{ $farm->catchcop }}</p>
                        @endif
                        <p class="txt">
                            @foreach ($farm->keywords as $keyword)
                                <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">#{{ $keyword->keyword }}</span>
                            @endforeach
                        </p>
                    </a>
                    </li>
                @endforeach
            </ul>
        </div>

</x-app-layout>