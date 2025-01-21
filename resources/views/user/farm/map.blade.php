<x-app-layout>
        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />
        <!-- パンくずリストの表示 -->
        <nav aria-label="breadcrumb" class="mt-10 mb-10">
            {!! Breadcrumbs::render('farm.index') !!}
        </nav>
        <div class="wrap">
            <div class="container mx-auto px-4 py-6">
                <h1 class="text-xl font-bold mb-4">牧場検索</h1>
        
                <!-- 検索フォーム -->
                <form action="{{ route('user.farm.index') }}" method="GET" class="mb-6">
                    <!-- キーワード検索 -->
                    <div class="mb-4">
                        <label for="keyword" class="block font-bold mb-2">キーワード検索</label>
                        <input type="text" id="keyword" name="keyword" value="{{ request('keyword') }}" class="w-full border border-gray-300 p-2 rounded">
                    </div>
        
                    <!-- 都道府県検索 -->
                    <div class="mb-4">
                        <label class="block font-bold mb-2">都道府県</label>
                        @foreach ($prefectures as $prefecture)
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="prefectures[]" value="{{ $prefecture }}" 
                                    {{ request()->has('prefectures') && in_array($prefecture, request('prefectures')) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $prefecture }}</span>
                            </label>
                        @endforeach
                    </div>
        
                    <!-- キーワード検索 -->
                    <div class="mb-4">
                        <label class="block font-bold mb-2">キーワード</label>
                        @foreach ($keywords as $keyword)
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="keywords[]" value="{{ $keyword->id }}" 
                                    {{ request()->has('keywords') && in_array($keyword->id, request('keywords')) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $keyword->keyword }}</span>
                            </label>
                        @endforeach
                    </div>
        
                    <!-- 種別検索 -->
                    <div class="mb-4">
                        <label class="block font-bold mb-2">種別</label>
                        @foreach ($kinds as $kind)
                            <label class="inline-flex items-center mr-4">
                                <input type="checkbox" name="kinds[]" value="{{ $kind->id }}" 
                                    {{ request()->has('kinds') && in_array($kind->id, request('kinds')) ? 'checked' : '' }}>
                                <span class="ml-2">{{ $kind->kind }}</span>
                            </label>
                        @endforeach
                    </div>
        
                    <!-- 検索ボタン -->
                    <div class="flex justify-center items-center">
                        <button type="submit" class="bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded">
                            検索
                        </button>
                    </div>
                </form>
        
                <!-- 検索結果 -->
                <ul class="list-none space-y-4">
                    @forelse ($farms as $farm)
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
                        <p class="text-gray-500">該当する牧場が見つかりませんでした。</p>
                    @endforelse
                </ul>
        </div>
    
    <!-- Session Status -->
    {{-- <x-auth-session-status class="mb-4" :status="session('status')" /> --}}

    {{-- <div class="flex"> --}}
        {{-- <div class="wrap">
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
                        <p class="pref">{{ $farm->prefecture }}</p>
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
                @endforeach
            </ul>
        </div> --}}

</x-app-layout>