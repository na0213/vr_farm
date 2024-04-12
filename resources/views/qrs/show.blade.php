<x-guestfarm-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="max-w-sm w-full lg:max-w-full lg:flex">
                <a href="{{ route('farm.show', ['id' => $farm->id]) }}">
                    <button class="ml-10 bg-transparent hover:bg-gray-500 text-gray-700 font-semibold hover:text-white py-2 px-4 border border-gray-500 hover:border-transparent rounded">
                        {{ $farm->farm_name }}へ戻る
                    </button>
                </a>
            </div>
            
            <div class="flex items-center justify-center">
                <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
                    <p>ありがとうございます！</p>
                    <p class="mb-5">保存してご使用ください</p>
                    <div class="flex items-center justify-center">
                        @if ($qr->image_path)
                            <img src="{{ $qr->image_path }}">
                        @else
                            <p>画像がアップロードされていません。</p>
                        @endif
                    </div>
                    
                </div>
            </div>
        </div>

    </div>
   

</x-guestfarm-layout>