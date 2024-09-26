<x-admin-layout>
  <x-slot name="header">
      <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
          記事の詳細
      </h2>
  </x-slot>

  <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              <div class="p-6 text-gray-900 dark:text-gray-100">
                  <!-- 牧場名 -->
                  <h3 class="text-lg font-bold mb-4">牧場名: {{ $article->farm->farm_name }}</h3>

                  <!-- タイトル -->
                  <h4 class="text-md font-bold mb-4">タイトル: {{ $article->title }}</h4>

                  <!-- 記事内容 -->
                  <div class="mb-4">
                      <p>{!! $article->article_content !!}</p> <!-- HTMLエンティティのサニタイズ -->
                  </div>

                  <!-- 画像表示 -->
                  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                      @foreach (json_decode($article->article_images, true) as $image)
                          <div class="image-preview">
                              <img src="{{ $image }}" alt="Article Image" class="w-full h-auto rounded shadow-md">
                          </div>
                      @endforeach
                  </div>

                  <!-- 戻るボタン -->
                  <div class="mt-6">
                      <a href="{{ route('admin.backend.article.index') }}" class="text-white bg-gray-500 border-0 py-2 px-8 focus:outline-none hover:bg-gray-600 rounded text-lg">
                          戻る
                      </a>
                  </div>
              </div>
          </div>
      </div>
  </div>
</x-admin-layout>
