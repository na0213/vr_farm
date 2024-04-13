<x-app-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="wrap">
        <h1 class="title-start">F<span class="title-font">ARM</h1>
        <ul class="grid ">
            @forelse ($favorites as $farm)
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
                                <img src="../storage/cow.png" alt="Cow">
                                @break
                            @case(2)
                                <img src="../storage/ushi.png" alt="Ushi">
                                @break
                            @case(3)
                                <img src="../storage/bird.png" alt="Bird">
                                @break
                            @case(4)
                                <img src="../storage/pig.png" alt="Pig">
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
            @empty
            <div class="m-10">
                <li>お気に入り登録はありません。</li>
            </div>
            @endforelse
        </ul>
        <h1 class="text-start mt-10">F<span class="title-font">OLLOW</h1>
        <ul class="grid">
            @forelse ($followings as $mypage)
                <li class="card rounded overflow-hidden shadow-lg">
                    <a href="{{ route('user.mypage.public_show', ['mypage' => $mypage->id]) }}">
                        <div class="mypage mt-10 ml-10 w-1/6">
                            <img src="{{ $mypage->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
                        </div>
                        <p class="ml-5">{{ $mypage->nickname ?? '名無しさん' }}</p>
                        {{-- <p class="ml-5">{{ $mypage->catchphrase ?? '' }}</p> --}}
                    </a>
                </li>
            @empty
                <div class="m-10">
                    <li>フォローはありません。</li>
                </div>
            @endforelse
        </ul>
    </div>

</x-app-layout>