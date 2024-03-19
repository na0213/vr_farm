<x-adminfarm-layout>
  <div class="top-title">
    <h1 class="top-content">
      <span class="farm-name">{{ $owner->farm->farm_name }}</span>
      <span class="farm-address">{{ $owner->farm->prefecture }}</span>
    </h1>
  </div>

  <div class="w-full mt-20">
      {{-- {!! $farm->vr !!} --}}
  </div>

  <div class="flex items-center justify-center">
      <p>{{ $owner->farm->theme }}</p>
  </div>

  <div class="flex items-center justify-center">
      <h2>{{ $owner->farm->catchcopy }}</h2>
  </div>
  <div class="flex items-center justify-center">
      <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
          <div>Story</div>
          <div class="cp_box">
              <input id="cp01" type="checkbox">
              <label for="cp01"></label>
              <div class="cp_container">
                  <p>{!! $owner->farm->farm_info !!}</p>
              </div>
          </div>
      </div>
  </div>

  <div class="flex items-center justify-center">
      <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
          <div>Raise</div>
          <div class="cp_box">
              <input id="cp02" type="checkbox">
              <label for="cp02"></label>
              <div class="cp_container">
                  <p class="mb-4">何を食べて育っているの？<br>どんな環境で育っているの？</p>
                  @foreach ($owner->farm->animals as $animal)
                  <div class="flex flex-col rounded-lg mb-2 bg-gray-100 text-surface shadow-secondary-1 dark:bg-surface-dark dark:text-white md:flex-row">                        <img
                        class="h-96 w-full rounded-t-lg object-cover md:h-auto md:w-64 md:!rounded-none md:!rounded-s-lg"
                        src="{{ $animal->animal_image }}"
                        alt="" />
                      <div class="flex flex-col justify-start p-6">
                        <h5 class="mb-2 text-xl font-medium">{{ $animal->animal_name }}</h5>
                        <p class="mb-4 text-base">{{ $animal->animal_info }}</p>
                      </div>
                  </div>
                  @endforeach
              </div>
          </div>
      </div>
  </div>

  <div class="flex items-center justify-center">
    <div class="w-4/5 mt-20 p-5 rounded overflow-hidden shadow-lg">
        <div>Store</div>
        <div class="cp_box">
          <input id="cp03" type="checkbox">
          <label for="cp03"></label>
          <div class="cp_container">
              <p class="mb-4">商品・販売店一覧</p>
              @foreach ($owner->farm->stores as $store)
              <div class="flex flex-col rounded-lg mb-2 bg-gray-100 text-surface shadow-secondary-1 dark:bg-surface-dark dark:text-white md:flex-row">
                
                <img class="h-96 w-full rounded-t-lg object-cover md:h-auto md:w-64 md:!rounded-none md:!rounded-s-lg" src="{{ $store->product_image }}" alt="" />
                  <a href="{{ $store->product_link }}" target="_blank">
                    <div class="flex flex-col justify-start p-6">
                      <h5 class="mb-2 text-xl font-medium">{{ $store->product_name }}</h5>
                      <p class="mb-4 text-base">{{ $store->product_info }}</p>
                    </div>
                  </a>
              </div>
              @endforeach
          </div>
      </div>
    </div>
  </div>

</x-adminfarm-layout>