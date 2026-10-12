<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Farm;
use App\Models\PurchasedItem;
use App\Services\ImageStorage;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * 購入した商品(牧場ごと)の登録・編集・削除。写真は3枚まで。
 */
class PurchasedItemController extends Controller
{
    // 登録済みの一覧と、新規登録のフォーム
    public function create(string $farmId)
    {
        $farm = Farm::with('purchasedItems')->findOrFail($farmId);

        return view('backend.purchased_items.create', compact('farm'));
    }

    public function store(Request $request, string $farmId)
    {
        $farm = Farm::findOrFail($farmId);
        $validated = $request->validate($this->rules(firstImageRequired: true));

        $item = new PurchasedItem([
            'item_name' => $validated['item_name'],
            'item_comment' => $validated['item_comment'] ?? null,
            'item_link' => $validated['item_link'] ?? null,
        ]);
        foreach (PurchasedItem::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                $item->{$field} = $this->storeImage($request->file($field));
            }
        }
        $farm->purchasedItems()->save($item);

        return redirect()->route('admin.backend.purchased-items.create', ['farm' => $farm->id])
            ->with('success', '登録しました。');
    }

    public function edit(string $id)
    {
        $item = PurchasedItem::with('farm')->findOrFail($id);

        return view('backend.purchased_items.edit', compact('item'));
    }

    public function update(Request $request, string $id)
    {
        $item = PurchasedItem::findOrFail($id);
        $validated = $request->validate($this->rules(firstImageRequired: false));

        $item->item_name = $validated['item_name'];
        $item->item_comment = $validated['item_comment'] ?? null;
        $item->item_link = $validated['item_link'] ?? null;

        // 新しい写真を選んだ欄は差し替え、「消す」に印を付けた欄(2・3枚目だけ)は空にする
        $oldImages = [];
        foreach (PurchasedItem::IMAGE_FIELDS as $field) {
            if ($request->hasFile($field)) {
                $oldImages[] = $item->{$field};
                $item->{$field} = $this->storeImage($request->file($field));
            } elseif ($field !== 'item_image' && $request->boolean('remove_'.$field)) {
                $oldImages[] = $item->{$field};
                $item->{$field} = null;
            }
        }

        $item->save();
        array_map([ImageStorage::class, 'delete'], $oldImages);

        return redirect()->route('admin.backend.purchased-items.create', ['farm' => $item->farm_id])
            ->with('success', '更新しました。');
    }

    public function destroy(string $id)
    {
        $item = PurchasedItem::findOrFail($id);
        $item->delete();
        array_map([ImageStorage::class, 'delete'], $item->images());

        return redirect()->route('admin.backend.purchased-items.create', ['farm' => $item->farm_id])
            ->with('success', '削除しました。');
    }

    private function rules(bool $firstImageRequired): array
    {
        // HEIC は読めないので JPEG / PNG / WebP だけ
        $image = ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'];

        return [
            'item_name' => 'required|string|max:255',
            'item_comment' => 'nullable|string|max:2000',
            'item_link' => 'nullable|url:http,https|max:2048',
            'item_image' => [$firstImageRequired ? 'required' : 'nullable', ...$image],
            'item_image_2' => ['nullable', ...$image],
            'item_image_3' => ['nullable', ...$image],
        ];
    }

    private function storeImage(UploadedFile $file): string
    {
        // 3枚を続けて保存しても名前がぶつからないよう、ULID(時刻+乱数)を使う
        return ImageStorage::storeResized($file, 'purchased_item_images/'.Str::lower((string) Str::ulid()).'.jpg', maxWidth: 1200);
    }
}
