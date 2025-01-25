<x-top-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    {{-- <div class="mt-20 flex justify-center"> --}}

    <form method="POST" action="{{ route('contact.send') }}">
        @csrf
        <!-- 幅を広げるために max-w-4xl に変更 -->
        <div class="m-10 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
            <h5 class="font-semibold dark:text-white mb-6">まだ送信されていません</h5>
    
            <!-- お名前 -->
            <div class="mb-4">
                <label for="send_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">お名前</label>
                <!-- フォーム内容も幅広げるために w-full を維持 -->
                <p class="bg-gray-50 border border-gray-300 text-sm rounded-lg p-4 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    {{ $postarr["send_name"] }}
                </p>
            </div>
    
            <!-- メールアドレス -->
            <div class="mb-4">
                <label for="send_email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">メールアドレス</label>
                <p class="bg-gray-50 border border-gray-300 text-sm rounded-lg p-4 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    {{ $postarr["send_email"] }}
                </p>
            </div>
    
            <!-- メッセージ内容 -->
            <div class="mb-6">
                <label for="send_message" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">メッセージ内容</label>
                <p class="bg-gray-50 border border-gray-300 text-sm rounded-lg p-4 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    {!! nl2br(e($postarr["send_message"])) !!}
                </p>
            </div>
    
            <!-- 送信確認メッセージ -->
            <div class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-6">上記の内容で送信します</div>
    
            <!-- 送信ボタンと戻るボタン -->
            <div class="flex justify-center">
                <button type="submit" name="action" value="back" class="bg-gray-500 text-white px-6 py-2 rounded-lg mr-4 hover:bg-gray-600">戻る</button>
                <button type="submit" name="action" value="submit" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">送信</button>
            </div>
            
            <!-- hiddenでデータを再送 -->
            @foreach($postarr as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
        </div>
    </form>

    {{-- </div> --}}
</x-top-layout>
