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

use Illuminate\Support\Str;
// use App;
// use Illuminate\Database\Eloquent\ModelNotFoundException;

class FarmController extends Controller
{
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
                Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                $farm->vr = Storage::disk('s3')->url($fileName);
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

    public function show($id)
    {
        $owner = Owner::with('farm')->findOrFail($id);
        // $farm = $owner->farm;
        // $farm = Farm::findOrFail($id);
        $kinds = Kind::all();
        $keywords = Keyword::all();
    
        return view('backend.farms.show', compact('owner', 'kinds', 'keywords'));
    }

    public function edit(string $id)
    {
        $farm = Farm::with(['kinds', 'keywords'])->findOrFail($id);
        $owner = $farm->owner; // 仮定すると、Farmモデルにはownerというリレーションが定義されている必要があります
        $images = $farm->images; // タイプミスの修正: 'iamges' -> 'images'
        $selected_kinds = $farm->kinds->pluck('id')->toArray();
        $selected_keywords = $farm->keywords->pluck('id')->toArray();
        $kinds = Kind::all();
        $keywords = Keyword::all();
    
        return view('backend.farms.edit', compact('owner', 'farm', 'images', 'kinds', 'keywords', 'selected_kinds', 'selected_keywords'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'catchcopy' => 'nullable|string|max:500',
            'prefecture' => 'required|string|max:255',
            'address' => 'nullable|string',
            // 画像バリデーション
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
            DB::beginTransaction();
    
            $farm = Farm::findOrFail($id);
            $farm->farm_name = $validated['name'];
            $farm->catchcopy = $validated['catchcopy'];
            $farm->prefecture = $validated['prefecture'];
            $farm->address = $validated['address'];
            $farm->theme = $validated['theme'];
            $farm->hp_link = $validated['hp_link'];
            $farm->has_experience = $validated['has_experience'] ?? false;
            $farm->instagram_link = $validated['instagram_link'];
            $farm->is_published = $validated['is_published'];

            // ▼▼▼ ここから画像処理（削除＆追加） ▼▼▼
            if ($request->hasFile('vr')) {
                // 1. 既存の画像があればS3から削除
                if ($farm->vr) {
                    // 以前のデータが<iframe>タグなどの場合はURLではないので、URLの時だけ削除を実行
                    if (filter_var($farm->vr, FILTER_VALIDATE_URL)) {
                        // URLからパス部分（例: /farm_vr/xxx.jpg）を抽出
                        $existingImagePath = parse_url($farm->vr, PHP_URL_PATH);
                        // 先頭の / を削除してS3のキーとして扱えるようにする
                        $existingImagePath = ltrim($existingImagePath, '/');
                        
                        // S3から削除実行
                        if (Storage::disk('s3')->exists($existingImagePath)) {
                            Storage::disk('s3')->delete($existingImagePath);
                        }
                    }
                }

                // 2. 新しい画像をS3にアップロード
                $image = $request->file('vr');
                $fileName = 'farm_vr/' . uniqid() . '.jpg';
                Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                
                // 新しいURLをセット
                $farm->vr = Storage::disk('s3')->url($fileName);
            }
            // ▲▲▲ 画像処理ここまで ▲▲▲

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
    
    // image
    public function images($id)
    {
        $owner = Owner::with('farm')->findOrFail($id);
        $farm = Farm::findOrFail($id);
        return view('backend.farms.image', compact('owner', 'farm'));
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
                        // 1. S3にアップロード
                        $fileName = Str::uuid()->toString() . '.jpg';
                        $dir = 'farms';
                        
                        // putFileAsは成功するとパスを返します
                        $path = Storage::disk('s3')->putFileAs($dir, $imageFile, $fileName, 'public');
                        
                        if (!$path) {
                            throw new Exception('S3へのアップロードに失敗しました。');
                        }

                        // エラー時に削除できるようにパスを記録しておく
                        $uploadedPaths[] = $path;

                        $url = Storage::disk('s3')->url($path);

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

            // 2. S3にアップロードしてしまった画像を削除
            foreach ($uploadedPaths as $path) {
                if (Storage::disk('s3')->exists($path)) {
                    Storage::disk('s3')->delete($path);
                }
            }

            // エラーログを残す（デバッグ用）
            Log::error('画像アップロードエラー: ' . $e->getMessage());

            return back()->withInput()->withErrors(['error' => '保存に失敗しました。もう一度お試しください。']);
        }
    }

    public function editImages($farmId)
    {
        // 'owner'リレーションを含めて$farmを取得
        $farm = Farm::with('farmImages', 'owner')->findOrFail($farmId);
        $owner = $farm->owner; // $farmに紐づく$ownerを取得
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

                // 1. 新しい画像をS3にアップロード
                $fileName = Str::uuid()->toString() . '.jpg';
                $dir = 'farms';
                $path = Storage::disk('s3')->putFileAs($dir, $imageFile, $fileName, 'public');

                if (!$path) {
                    throw new Exception('S3へのアップロードに失敗しました。');
                }

                $newUploadedPath = $path;
                $url = Storage::disk('s3')->url($path);

                // 古い画像のパスを取得（削除用だが、DB更新成功後に消す）
                $oldImagePath = parse_url($image->image_path, PHP_URL_PATH);
                // パスの先頭にスラッシュがある場合、削除調整が必要な場合があります（環境による）
                $oldImagePath = ltrim($oldImagePath, '/'); 

                // 2. データベース更新
                $image->update([
                    'image_path' => $url,
                ]);

                // 3. 成功したので、古い画像をS3から削除
                // (重要: 更新処理より前に消すと、更新失敗時に画像がなくなるリスクがあるため最後に消す)
                if ($oldImagePath && Storage::disk('s3')->exists($oldImagePath)) {
                    Storage::disk('s3')->delete($oldImagePath);
                }

                DB::commit();

                return redirect()->route('admin.backend.owners.show', ['id' => $farm->owner_id])
                    ->with('success', '画像が更新されました。');
            }

            // 画像が選択されていない場合
            return back()->withInput()->withErrors(['error' => '画像が選択されていません。']);

        } catch (Exception $e) {
            DB::rollBack();

            // エラー時は、今回アップロードしようとした「新しい画像」を削除
            if ($newUploadedPath && Storage::disk('s3')->exists($newUploadedPath)) {
                Storage::disk('s3')->delete($newUploadedPath);
            }

            Log::error('画像更新エラー: ' . $e->getMessage());

            return back()->withInput()->withErrors(['error' => '画像の更新に失敗しました。']);
        }
    }
    
    public function deleteImage($farmId, $imageId)
    {
        $image = FarmImage::findOrFail($imageId);
        
        Storage::disk('s3')->delete(parse_url($image->image_path, PHP_URL_PATH));

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

    public function destroy(string $id)
    {
        //
    }

}
