<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('contact.send') }}">
        @csrf

        <p>
          お名前：{{$postarr["send_name"]}}
          </p>
          <p>
          メールアドレス：{{$postarr["send_mail"]}}
          </p>
          <p>
          メッセージ内容：<br>
          {!! nl2br($postarr["message"]) !!}
          </p>
          <input type="submit" name="action" value="submit">
           <span class="backbtn">
          <input type="submit" name="action" value="back">
          </span>
          @foreach($postarr as $key => $value)
          <input type="hidden" name="{{$key}}" value="{{$value}}">
          @endforeach

    </form>
</x-guest-layout>
