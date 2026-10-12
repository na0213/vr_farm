{{-- 購入した商品の入力欄(登録・編集で共通)。$item は編集時だけ渡す --}}
@php($item = $item ?? null)

@if ($errors->any())
    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
        <ul class="list-disc pl-5">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="space-y-5">
    <div>
        <label for="item_name" class="block text-sm font-medium text-gray-700">商品名<span class="text-red-600">(必須)</span></label>
        <input type="text" id="item_name" name="item_name" value="{{ old('item_name', $item?->item_name) }}" required maxlength="255"
               class="mt-1 w-full rounded border-gray-300 focus:border-yellow-500 focus:ring-yellow-200">
    </div>

    <div>
        <label for="item_comment" class="block text-sm font-medium text-gray-700">感想(ひとこと)</label>
        <p class="text-xs text-gray-500">食べた・飲んだ正直な感想。公開ページにそのまま出ます(改行もそのまま)。</p>
        <textarea id="item_comment" name="item_comment" rows="4" maxlength="2000"
                  class="mt-1 w-full rounded border-gray-300 focus:border-yellow-500 focus:ring-yellow-200">{{ old('item_comment', $item?->item_comment) }}</textarea>
    </div>

    <div>
        <label for="item_link" class="block text-sm font-medium text-gray-700">販売ページのURL</label>
        <p class="text-xs text-gray-500">牧場の公式通販など。無ければ空欄でOK(ボタンが出ません)。</p>
        <input type="url" id="item_link" name="item_link" value="{{ old('item_link', $item?->item_link) }}" placeholder="https://"
               class="mt-1 w-full rounded border-gray-300 focus:border-yellow-500 focus:ring-yellow-200">
    </div>

    <fieldset>
        <legend class="block text-sm font-medium text-gray-700">写真(3枚まで)</legend>
        <p class="text-xs text-gray-500">
            自分で撮った写真。JPEG・PNG・WebP、1枚5MBまで(iPhone の HEIC は不可)。1枚目が一覧に出ます。@if ($item)変えるときだけ選んでください。@endif
        </p>
        <div class="mt-2 flex flex-col gap-4 sm:flex-row">
            @foreach ([
                'item_image' => '1枚目(パッケージなど)',
                'item_image_2' => '2枚目(裏面など)',
                'item_image_3' => '3枚目(中身など)',
            ] as $field => $label)
                @php($current = $item?->{$field})
                <div class="rounded border border-gray-200 p-3 sm:flex-1 sm:min-w-0">
                    <label for="{{ $field }}" class="block text-sm text-gray-700">
                        {{ $label }}@if ($field === 'item_image' && ! $item)<span class="text-red-600">(必須)</span>@endif
                    </label>
                    <input type="file" id="{{ $field }}" name="{{ $field }}" accept="image/jpeg,image/png,image/webp"
                           @if ($field === 'item_image' && ! $item) required @endif
                           onchange="previewItemImage(this, '{{ $field }}_preview')"
                           class="mt-1 block w-full text-xs text-gray-900 bg-gray-50 rounded border border-gray-300 cursor-pointer">
                    <img id="{{ $field }}_preview" src="{{ $current }}" alt="" class="mt-2 w-full aspect-square object-cover rounded @if (! $current) hidden @endif">
                    @if ($current && $field !== 'item_image')
                        <label class="mt-2 flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="remove_{{ $field }}" value="1" class="rounded border-gray-300">
                            この写真を消す
                        </label>
                    @endif
                </div>
            @endforeach
        </div>
    </fieldset>
</div>

<script>
    function previewItemImage(input, previewId) {
        const preview = document.getElementById(previewId);
        const file = input.files && input.files[0];
        if (!file) return;
        if (file.size > 5 * 1024 * 1024) {
            alert('写真は5MB以下にしてください。');
            input.value = '';
            return;
        }
        preview.src = URL.createObjectURL(file);
        preview.classList.remove('hidden');
    }
</script>
