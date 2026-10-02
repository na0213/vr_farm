<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Owner;
use App\Models\Farm;
use App\Models\FarmImage;
use App\Models\Kind;
use App\Models\Keyword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;
use App\Services\ImageStorage;
use Illuminate\Support\Str;

class FarmController extends Controller
{
    public function index()
    {
        $farms = Farm::with('owner:id,name')
            ->withCount(['animals', 'products', 'stores'])
            ->orderBy('farm_name')
            ->get();

        return view('backend.farms.index', compact('farms'));
    }

    public function create($ownerId)
    {
        $owner = Owner::findOrFail($ownerId);
        $kinds = Kind::all();
        $keywords = Keyword::all();
        return view('backend.farms.create', compact('owner', 'kinds', 'keywords'));
    }

    public function store(Request $request, $ownerId)
    {
        // バリデーション
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'catchcopy' => 'nullable|string|max:500',
            'prefecture' => 'required|string|max:255',
            'address' => 'nullable|string',
            'vr' => 'nullable|image|max:10240',
            'theme' => 'nullable|string|max:255',
            'hp_link' => 'nullable|url|max:500',
            'has_experience' => 'nullable|boolean',
            'instagram_link' => 'nullable|url|max:500',
            'kinds' => 'nullable|array',
            'kinds.*' => 'exists:kinds,id',
            'keywords' => 'nullable|array',
            'keywords.*' => 'exists:keywords,id',
            'is_published' => 'required|boolean',
        ]);
    
        try {
            // トランザクション開始
            DB::beginTransaction();
    
            $owner = Owner::findOrFail($ownerId);
            $farm = new Farm();
            $farm->owner_id = $owner->id;
            $farm->farm_name = $validated['name'];
            $farm->catchcopy = $validated['catchcopy'];
            $farm->prefecture = $validated['prefecture'];
            $farm->address = $validated['address'];
            $farm->theme = $validated['theme'];
            $farm->hp_link = $validated['hp_link'];
            $farm->has_experience = $validated['has_experience'] ?? false;
            $farm->instagram_link = $validated['instagram_link'];
            $farm->is_published = $validated['is_published'];

            if ($request->hasFile('vr')) {
                $image = $request->file('vr');
                $fileName = 'farm_vr/' . uniqid() . '.jpg';
                $farm->vr = ImageStorage::storeOriginal($image, $fileName); // VRは画質が落ちるのでリサイズしない
            }

            $farm->save();
    
            // kinds との関連付け
            if (!empty($validated['kinds'])) {
                $farm->kinds()->attach($validated['kinds']);
            }
    
            // keywords との関連付け
            if (!empty($validated['keywords'])) {
                $farm->keywords()->attach($validated['keywords']);
            }
    
            // トランザクションコミット
            DB::commit();
    
            return redirect()->route('admin.backend.owners.show', ['id' => $ownerId])
                ->with('message', '牧場が登録されました');
        } catch (\Exception $e) {
            // エラーが発生した場合はロールバック
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }
    }

    public function edit(string $id)
    {
        $farm = Farm::with(['kinds', 'keywords'])->findOrFail($id);
        $owner = $farm->owner;
        $selected_kinds = $farm->kinds->pluck('id')->toArray();
        $selected_keywords = $farm->keywords->pluck('id')->toArray();
        $kinds = Kind::all();
        $keywords = Keyword::all();
    
        return view('backend.farms.edit', compact('owner', 'farm', 'kinds', 'keywords', 'selected_kinds', 'selected_keywords'));
    }

public function update(Request $request, $id)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'catchcopy' => 'nullable|string|max:500',
        'prefecture' => 'required|string|max:255',
        'address' => 'nullable|string',

        // VR画像
        'vr' => 'nullable|image|max:10240', // 10MB

        'theme' => 'nullable|string|max:255',
        'hp_link' => 'nullable|url|max:500',
        'has_experience' => 'nullable|boolean',
        'instagram_link' => 'nullable|url|max:500',
        'kinds' => 'nullable|array',
        'kinds.*' => 'exists:kinds,id',
        'keywords' => 'nullable|array',
        'keywords.*' => 'exists:keywords,id',
        'is_published' => 'required|boolean',

        // VR削除チェック
        'delete_vr' => 'nullable|boolean',
    ]);

    try {
        DB::beginTransaction();

        $farm = Farm::findOrFail($id);

        $farm->farm_name = $validated['name'];
        $farm->catchcopy = $validated['catchcopy'] ?? null;
        $farm->prefecture = $validated['prefecture'];
        $farm->address = $validated['address'] ?? null;
        $farm->theme = $validated['theme'] ?? null;
        $farm->hp_link = $validated['hp_link'] ?? null;
        $farm->has_experience = $validated['has_experience'] ?? false;
        $farm->instagram_link = $validated['instagram_link'] ?? null;
        $farm->is_published = $validated['is_published'];

        // ▼▼▼ VR画像処理（削除 → 置き換え） ▼▼▼
        $deleteVr = (bool) ($request->input('delete_vr') ?? false);

        // 「削除」または「新規アップロード」がある場合は、まず既存を消す
        if ($deleteVr || $request->hasFile('vr')) {

            ImageStorage::delete($farm->vr);

            // 削除チェックが入っているならDB上もnullにする
            if ($deleteVr) {
                $farm->vr = null;
            }

            // 新しいファイルがあるならアップロードして上書き
            if ($request->hasFile('vr')) {
                $image = $request->file('vr');

                // 拡張子を実ファイルに合わせる（jpg固定だとpng等で不一致になるので）
                $ext = $image->getClientOriginalExtension() ?: 'jpg';
                $fileName = 'farm_vr/' . uniqid() . '.' . $ext;

                $farm->vr = ImageStorage::storeOriginal($image, $fileName); // VRは画質が落ちるのでリサイズしない
            }
        }
        // ▲▲▲ VR画像処理ここまで ▲▲▲

        $farm->save();

        // kinds と keywords の関連付けを更新
        $farm->kinds()->sync($validated['kinds'] ?? []);
        $farm->keywords()->sync($validated['keywords'] ?? []);

        DB::commit();

        return redirect()->route('admin.backend.owners.show', ['id' => $farm->owner_id])
            ->with('message', '牧場の情報が更新されました');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error($e->getMessage());

        return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
    }
}

    
    public function storeImages(Request $request, $id)
    {
        $farm = Farm::findOrFail($id);
        
        // アップロードしたファイルのパスを一時保存する配列（エラー時の削除用）
        $uploadedPaths = [];

        // トランザクション開始
        DB::beginTransaction();

        try {
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $index => $imageFile) {
                    if ($imageFile->isValid()) {
                        // 1. リサイズして保存
                        $path = 'farms/' . Str::uuid()->toString() . '.jpg';
                        $url = ImageStorage::storeResized($imageFile, $path);

                        // エラー時に削除できるようにパスを記録しておく
                        $uploadedPaths[] = $path;

                        // 2. データベース保存
                        FarmImage::create([
                            'farm_id' => $farm->id,
                            'image_path' => $url,
                            'image_order' => $index + 1,
                        ]);
                    }
                }
            }

            // 全て成功したらコミット（確定）
            DB::commit();

            return redirect()->route('admin.backend.owners.show', ['id' => $farm->owner_id])
                ->with('success', '画像が正常にアップロードされました。');

        } catch (Exception $e) {
            // エラーが発生した場合

            // 1. データベースをロールバック（書き込みを取り消し）
            DB::rollBack();

            // 2. 保存してしまった画像を削除
            Storage::disk(ImageStorage::DISK)->delete($uploadedPaths);

            // エラーログを残す（デバッグ用）
            Log::error('画像アップロードエラー: ' . $e->getMessage());

            return back()->withInput()->withErrors(['error' => '保存に失敗しました。もう一度お試しください。']);
        }
    }

    public function editImages($farmId)
    {
        $farm = Farm::with('farmImages', 'owner')->findOrFail($farmId);
        $owner = $farm->owner;
        return view('backend.farms.edit-image', compact('farm', 'owner'));
    }

    public function updateImage(Request $request, $farmId, $imageId)
    {
        $farm = Farm::findOrFail($farmId);
        $image = FarmImage::findOrFail($imageId);
        
        // 新しくアップロードされたファイルのパス（エラー時の削除用）
        $newUploadedPath = null;

        DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                $imageFile = $request->file('image');

                // 1. 新しい画像をリサイズして保存
                $path = 'farms/' . Str::uuid()->toString() . '.jpg';
                $url = ImageStorage::storeResized($imageFile, $path);

                $newUploadedPath = $path;

                // 古い画像のURL（削除用だが、DB更新成功後に消す）
                $oldImageUrl = $image->image_path;

                // 2. データベース更新
                $image->update([
                    'image_path' => $url,
                ]);

                // 3. 成功したので、古い画像を削除
                // (重要: 更新処理より前に消すと、更新失敗時に画像がなくなるリスクがあるため最後に消す)
                ImageStorage::delete($oldImageUrl);

                DB::commit();

                return redirect()->route('admin.backend.owners.show', ['id' => $farm->owner_id])
                    ->with('success', '画像が更新されました。');
            }

            // 画像が選択されていない場合
            return back()->withInput()->withErrors(['error' => '画像が選択されていません。']);

        } catch (Exception $e) {
            DB::rollBack();

            // エラー時は、今回アップロードしようとした「新しい画像」を削除
            if ($newUploadedPath) {
                Storage::disk(ImageStorage::DISK)->delete($newUploadedPath);
            }

            Log::error('画像更新エラー: ' . $e->getMessage());

            return back()->withInput()->withErrors(['error' => '画像の更新に失敗しました。']);
        }
    }
    
    public function deleteImage($farmId, $imageId)
    {
        $image = FarmImage::findOrFail($imageId);
        
        ImageStorage::delete($image->image_path);

        // データベースから削除前に image_order を取得
        $deletedOrder = $image->image_order;

        // データベースから削除
        $image->delete();

        // 削除された画像より後の画像の順序を更新
        FarmImage::where('farm_id', $farmId)
                ->where('image_order', '>', $deletedOrder)
                ->decrement('image_order');
    
        return redirect()->route('admin.admin.backend.farms.editImages', ['farmId' => $farmId])->with('success', '画像が削除されました。');
    }

}
