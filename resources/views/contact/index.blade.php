{{-- <x-guest-layout> --}}
<x-top-layout>
<!-- Session Status -->
<x-auth-session-status class="mb-4" :status="session('status')" />

<div class="m-10 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
  <h5 class="flex justify-cente font-semibold dark:text-white mb-6">お問い合わせ</h5>
  <div class="mt-20 flex justify-center">
    <form method="POST" action="{{ route('contact.confirm') }}" class="w-4/5 max-w-lg">
        @csrf
        <div>
            <x-input-label for="name" :value="__('お名前')" />
            <x-text-input id="exampNameInput" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" 
            type="text" name="send_name" :value="old('send_name')" required autofocus autocomplete="send_name" />
            <x-input-error :messages="$errors->get('send_name')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="exampleEmailInput" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" 
            type="email" name="send_email" :value="old('send_email')" required autocomplete="send_email" />
            <x-input-error :messages="$errors->get('send_email')" class="mt-2" />
        </div>
        <div class="mt-4">
            <x-input-label for="exampleMessage" :value="__('お問い合わせ内容')" />
            <textarea id="exampleMessage" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out" 
            name="send_message" rows="4" required>{{ old('send_message') }}</textarea>
            <x-input-error :messages="$errors->get('send_message')" class="mt-2" />
        </div>
        <div class="mt-10 flex justify-center">
            <input class="button mr-4 bg-transparent hover:bg-gray-500 text-gray-700 font-semibold hover:text-white py-2 px-4 border border-gray-500 hover:border-transparent rounded" type="reset" value="リセット">
            <input class="button bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white py-2 px-4 border border-green-500 hover:border-transparent rounded" type="submit" value="確認">
        </div>
    </form>
  </div>
</div>

</x-top-layout>
{{-- </x-guest-layout> --}}
  {{-- <table class="table full-width">
      <tbody> --}}
        {{-- <tr>
          <th><label for="name">お名前</label></th>
          <td>
            <input class="full-width" type="text" name="send_name" placeholder="お名前" id="exampNameInput" required="required" value="{{old('send_name')}}">
          </td>
        </tr>
        <tr>
          <th><label for="email">メールアドレス</label></th>
          <td>
            <input class="full-width" type="email" name="send_mail" placeholder="メルアド" id="exampleEmailInput" required="required" value="{{old('send_mail')}}">
          </td>
        </tr> --}}
        {{-- <tr>
          <th><label for="exampleMessage">お問い合わせ内容</label></th>
          <td><textarea class="full-width textarea" name="message" placeholder="用件を書いてね" id="exampleMessage" required="required">{{old('message')}}</textarea></td>
        </tr>
      </tbody> --}}
    {{-- </table> --}}