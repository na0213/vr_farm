<footer class="footer py-8">
  <div class="container mx-auto">
    <div class="flex flex-col items-start">
      <div class="mb-2">
        <a href="{{ route('index') }}">
          <img src="{{ asset('storage/footer.png') }}" alt="牧場いきたい" class="w-24 md:w-32">
        </a>
      </div>
      <ul class="list-none w-full space-y-5">
        <li>
          <a href="{{ route('farm.index') }}" class="font-medium block footer-link">牧場検索</a>
        </li>
        <li>
          <a href="{{ route('contact.form') }}" class="font-medium block footer-link">問い合わせ</a>
        </li>
        <li>
          <a href="{{ route('about.index') }}" class="font-medium block footer-link">運営者情報</a>
        </li>
      </ul>
    </div>
    <p class="text-xs sm:text-sm mt-6 text-center footer-copy">
      &copy; WelfareFarm. All rights reserved.
    </p>
  </div>
</footer>

