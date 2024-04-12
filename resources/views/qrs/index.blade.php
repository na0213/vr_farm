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
                    <p>ご訪問いただきありがとうございます。</p>
                    <p>当サイトの<span class="font-bold">{{ $farm->farm_name }}</span>ページのQRコードをご用意しています。</p>
                    <p><span class="font-bold">{{ $farm->farm_name }}</span>の商品を販売される際に販促用POPなどにご使用いただけます。</p>
                    <p>以下のフォームにご入力いただき、QRコードをご利用ください。</p>
                    <form action="{{ route('qrs.store', ['farm' => $farm->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                    
                        <div class="mb-4">
                            <label for="name" class="block text-gray-700 text-sm font-bold mb-2">
                                お名前
                            </label>
                            <input name="name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="name">
                        </div>
                        <div class="mb-4">
                            <label for="shop_name" class="block text-gray-700 text-sm font-bold mb-2">
                                店舗名
                            </label>
                            <input name="shop_name" type="text" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="shop_name">
                        </div>
                        <div class="mb-6">
                            <label for="email" class="block text-gray-700 text-sm font-bold mb-2">
                                Email
                            </label>
                            <input name="email" type="email" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="email">
                        </div>
                        <div class="mb-6">
                            <label for="purpose" class="block text-gray-700 text-sm font-bold mb-2">
                                ご利用目的
                            </label>
                            <textarea name="purpose" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="purpose" placeholder="販促用POP"></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-teal-500 hover:bg-teal-700 text-white py-1 px-2 rounded">
                                QR表示
                            </button>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>

    </div>
   

</x-guestfarm-layout>