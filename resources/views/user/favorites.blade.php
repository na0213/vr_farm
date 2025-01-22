<x-app-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="wrap">
        <div class="container mx-auto px-4 py-6">
        <h1 class="title-start">F<span class="title-font">ARM</h1>
            <ul class="list-none space-y-4">
                @forelse ($favorites as $farm)
                    <li class="card flex items-center rounded overflow-hidden shadow-lg bg-white p-4">
                        <a href="{{ route('user.farm.show', ['id' => $farm->id]) }}" class="flex w-full">
                            <!-- 左側の画像 -->
                            @if($farm->farmImages->isNotEmpty())
                                <div class="farmimg w-1/3">
                                    <img src="{{ $farm->farmImages->first()->image_path }}" alt="{{ $farm->farm_name }}">
                                </div>
                            @else
                                <div class="farmimg w-1/3">
                                    <img src="../storage/noimage.jpg" alt="No image available">
                                </div>
                            @endif
                            <!-- 右側の詳細情報 -->
                            <div class="ml-4 flex-1">
                                <p class="ttl font-bold text-lg">{{ $farm->farm_name }}</p>
                                <p class="pref text-gray-600">{{ $farm->prefecture }}</p>
                                <div class="icon my-2">
                                    @foreach ($farm->kinds as $kind)
                                        @switch($kind->id)
                                            @case(1)
                                                <img src="../storage/cow.png" alt="Cow" class="inline-block w-6 h-6 mr-2">
                                                @break
                                            @case(2)
                                                <img src="../storage/ushi.png" alt="Ushi" class="inline-block w-6 h-6 mr-2">
                                                @break
                                            @case(3)
                                                <img src="../storage/bird.png" alt="Bird" class="inline-block w-6 h-6 mr-2">
                                                @break
                                            @case(4)
                                                <img src="../storage/pig.png" alt="Pig" class="inline-block w-6 h-6 mr-2">
                                                @break
                                        @endswitch
                                    @endforeach
                                </div>
                                @if (!empty($farm->catchcopy))
                                    <p class="text-gray-700">{{ $farm->catchcopy }}</p>
                                @endif
                                <p class="tag text-gray-500 mt-2">
                                    @foreach ($farm->keywords as $keyword)
                                        <span class="inline-block bg-gray-200 rounded-full px-3 py-1 text-sm font-semibold text-gray-700 mr-2 mb-2">#{{ $keyword->keyword }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </a>
                    </li>
                @empty
                    <p class="text-gray-500">お気に入り登録はありません。</p>
                @endforelse
            </ul>

        {{-- <h1 class="title-start mt-10">F<span class="title-font">OLLOW</h1>
        <ul class="grid">
            @forelse ($followings as $mypage)
                <li class="card rounded overflow-hidden shadow-lg">
                    <a href="{{ route('user.mypage.public_show', ['mypage' => $mypage->id]) }}">
                        <div class="mypage mt-10 ml-10 w-1/6">
                            <img src="{{ $mypage->my_image ?? asset('storage/noimage.jpg') }}" alt="Mypage Image">
                        </div>
                        <p class="ml-5">{{ $mypage->nickname ?? '名無しさん' }}</p>
                    </a>
                </li>
            @empty
                <div class="m-10">
                    <li>フォローはありません。</li>
                </div>
            @endforelse
        </ul> --}}
    </div>
    </div>

</x-app-layout>