<x-top-layout>
<x-slot name="title">お問い合わせ</x-slot>
{{--
  静的サイトなので、確認画面・完了画面はこのページの中で切り替える。
  送信は Cloudflare の Worker(worker/index.js の /api/contact)が受け取り、運営のアドレスにだけメールで届ける。
  ロボット対策に Cloudflare Turnstile を使う(サイトキーは config/services.php)。
--}}
@php($turnstileSiteKey = config('services.turnstile.site_key'))

<div class="m-10 p-6 bg-white border border-gray-200 rounded-lg shadow dark:bg-gray-800 dark:border-gray-700">
  {{-- 入力 --}}
  <section id="contact-input">
    <h5 class="flex justify-cente font-semibold dark:text-white mb-6">お問い合わせ</h5>
    <div class="mt-20 flex justify-center">
      <form id="contact-form" class="w-4/5 max-w-lg" novalidate>
          <div>
              <x-input-label for="send_name" :value="__('お名前')" />
              <x-text-input id="send_name" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
              type="text" name="send_name" maxlength="100" required autocomplete="name" />
          </div>
          <div class="mt-4">
              <x-input-label for="send_email" :value="__('Email')" />
              <x-text-input id="send_email" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
              type="email" name="send_email" maxlength="255" required autocomplete="email" />
          </div>
          <div class="mt-4">
              <x-input-label for="send_message" :value="__('お問い合わせ内容')" />
              <textarea id="send_message" class="block mt-1 w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-yellow-500 focus:bg-white focus:ring-2 focus:ring-yellow-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out"
              name="send_message" rows="4" maxlength="5000" required></textarea>
          </div>
          <p id="contact-input-error" class="mt-4 text-sm text-red-600" role="alert" hidden></p>
          <div class="mt-10 flex justify-center">
            <!-- リセットボタン -->
            <button type="reset" class="button mr-4 bg-transparent hover:bg-gray-500 text-xs sm:text-sm  text-gray-700 font-semibold hover:text-white py-2 px-4 border border-gray-500 hover:border-transparent rounded">リセット</button>

            <!-- 確認ボタン -->
            <input class="button bg-transparent hover:bg-green-500 text-green-700 font-semibold hover:text-white text-xs sm:text-sm  py-2 px-4 border border-green-500 hover:border-transparent rounded" type="submit" value="確認">
          </div>
      </form>
    </div>
  </section>

  {{-- 確認 --}}
  <section id="contact-confirm" hidden>
      <h5 class="font-semibold dark:text-white mb-6">まだ送信されていません</h5>

      <div class="mb-4">
          <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">お名前</p>
          <p class="bg-gray-50 border border-gray-300 text-sm rounded-lg p-4 dark:bg-gray-600 dark:border-gray-500 dark:text-white" data-confirm="send_name"></p>
      </div>
      <div class="mb-4">
          <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">メールアドレス</p>
          <p class="bg-gray-50 border border-gray-300 text-sm rounded-lg p-4 dark:bg-gray-600 dark:border-gray-500 dark:text-white" data-confirm="send_email"></p>
      </div>
      <div class="mb-6">
          <p class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">メッセージ内容</p>
          <p class="bg-gray-50 border border-gray-300 text-sm rounded-lg p-4 dark:bg-gray-600 dark:border-gray-500 dark:text-white whitespace-pre-wrap break-words" data-confirm="send_message"></p>
      </div>

      <div class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-6">上記の内容で送信します</div>
      @if ($turnstileSiteKey)
          <div id="contact-turnstile" class="mb-6 flex justify-center"></div>
      @endif
      <p id="contact-send-error" class="mb-6 text-sm text-red-600" role="alert" hidden></p>

      <div class="flex justify-center">
          <button type="button" id="contact-back" class="bg-gray-500 text-white px-6 py-2 rounded-lg mr-4 hover:bg-gray-600">戻る</button>
          <button type="button" id="contact-send" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 disabled:opacity-50">送信</button>
      </div>
  </section>

  {{-- 完了(控えのメールは送らないので、送った内容をここに表示する) --}}
  <section id="contact-complete" hidden tabindex="-1">
      <p class="font-semibold mb-4">お問い合わせを受け付けました。ありがとうございます。</p>
      <p class="text-sm text-gray-600 mb-6">控えのメールはお送りしていません。必要でしたら、この画面を保存してください。</p>
      <dl class="text-sm space-y-3">
          <div><dt class="font-medium">お名前</dt><dd data-complete="send_name"></dd></div>
          <div><dt class="font-medium">メールアドレス</dt><dd data-complete="send_email"></dd></div>
          <div><dt class="font-medium">お問い合わせ内容</dt><dd class="whitespace-pre-wrap break-words" data-complete="send_message"></dd></div>
      </dl>
      <div class="mt-10 flex justify-center">
          <a href="{{ route('index') }}" class="button bg-gray-500 text-white px-4 py-2 rounded">トップへ戻る</a>
      </div>
  </section>
</div>

<script>
  (() => {
    const siteKey = @json($turnstileSiteKey);
    const form = document.getElementById('contact-form');
    const sections = {
      input: document.getElementById('contact-input'),
      confirm: document.getElementById('contact-confirm'),
      complete: document.getElementById('contact-complete'),
    };
    const inputError = document.getElementById('contact-input-error');
    const sendError = document.getElementById('contact-send-error');
    const sendButton = document.getElementById('contact-send');
    const fields = ['send_name', 'send_email', 'send_message'];
    let values = {};
    let token = '';
    let widgetId = null;

    const show = (name) => {
      Object.entries(sections).forEach(([key, el]) => { el.hidden = key !== name; });
      window.scrollTo({ top: 0 });
    };
    const fill = (attr) => {
      fields.forEach((field) => { document.querySelector(`[${attr}="${field}"]`).textContent = values[field]; });
    };
    const setToken = (value) => {
      token = value;
      sendButton.disabled = siteKey ? token === '' : false;
    };

    form.addEventListener('submit', (event) => {
      event.preventDefault();
      fields.forEach((field) => { form[field].value = form[field].value.trim(); });
      if (!form.checkValidity()) {
        inputError.textContent = 'お名前・メールアドレス・お問い合わせ内容を、正しく入力してください。';
        inputError.hidden = false;
        form.reportValidity();
        return;
      }
      inputError.hidden = true;
      values = Object.fromEntries(fields.map((field) => [field, form[field].value]));
      fill('data-confirm');
      sendError.hidden = true;
      show('confirm');
      renderTurnstile();
      setToken(token);
    });

    // ロボット対策(確認画面を初めて開いたときに表示する。スクリプトの読み込みが後になったら、読み込み後に表示する)
    const renderTurnstile = () => {
      if (!siteKey || widgetId !== null || !window.turnstile || sections.confirm.hidden) return;
      widgetId = turnstile.render('#contact-turnstile', {
        sitekey: siteKey,
        language: 'ja',
        callback: setToken,
        'expired-callback': () => setToken(''),
        'error-callback': () => setToken(''),
      });
    };
    window.farm360TurnstileReady = renderTurnstile;

    document.getElementById('contact-back').addEventListener('click', () => show('input'));

    sendButton.addEventListener('click', async () => {
      sendButton.disabled = true;
      sendError.hidden = true;
      try {
        const response = await fetch('/api/contact', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ ...values, turnstile_token: token }),
        });
        if (!response.ok) throw new Error(`HTTP ${response.status}`);
        fill('data-complete');
        form.reset();
        show('complete');
        sections.complete.focus();
      } catch (error) {
        sendError.textContent = '送信できませんでした。時間をおいて、もう一度お試しください。';
        sendError.hidden = false;
        if (siteKey && widgetId !== null) {
          turnstile.reset(widgetId); // トークンは1回しか使えないので取り直す
          setToken('');
        } else {
          sendButton.disabled = false;
        }
      }
    });
  })();
</script>
@if ($turnstileSiteKey)
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit&onload=farm360TurnstileReady" async defer></script>
@endif
</x-top-layout>
