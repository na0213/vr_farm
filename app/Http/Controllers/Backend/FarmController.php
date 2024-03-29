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
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
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
            'vr' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'kinds' => 'nullable|array',
            'kinds.*' => 'exists:kinds,id',
            'keywords' => 'nullable|array',
            'keywords.*' => 'exists:keywords,id',
            'farm_info' => 'nullable|string',
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
            $farm->vr = $validated['vr'];
            $farm->theme = $validated['theme'];
            $farm->farm_info = $request->input('editor1'); // CKEditor からの入力を直接取得
            $farm->is_published = $validated['is_published'];

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
        // $owner = Owner::with('farm')->findOrFail($id);
        // $farm = Farm::with(['kinds', 'keywords'])->findOrFail($id);
        // $images = $farm->iamges;
        // $selected_kinds = $farm->kinds->pluck('id')->toArray();
        // $selected_keywords = $farm->keywords->pluck('id')->toArray();
        // $kinds = Kind::all();
        // $keywords = Keyword::all();

        // return view('backend.farms.edit', compact('owner', 'farm', 'images', 'kinds', 'keywords', 'selected_kinds', 'selected_keywords'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'catchcopy' => 'nullable|string|max:500',
            'prefecture' => 'required|string|max:255',
            'address' => 'nullable|string',
            'vr' => 'nullable|string',
            'theme' => 'nullable|string|max:255',
            'kinds' => 'nullable|array',
            'kinds.*' => 'exists:kinds,id',
            'keywords' => 'nullable|array',
            'keywords.*' => 'exists:keywords,id',
            'farm_info' => 'nullable|string',
            'is_published' => 'required|boolean',
        ]);
    
        try {
            DB::beginTransaction();
    
            $farm = Farm::findOrFail($id);
            $farm->farm_name = $validated['name'];
            $farm->catchcopy = $validated['catchcopy'];
            $farm->prefecture = $validated['prefecture'];
            $farm->address = $validated['address'];
            $farm->vr = $validated['vr'];
            $farm->theme = $validated['theme'];
            $farm->farm_info = $request->input('editor1'); // CKEditor からの入力
            $farm->is_published = $validated['is_published'];
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
    
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $imageFile) {
                // ファイルのアップロードが成功したか確認
                if ($imageFile->isValid()) {
                    // S3に直接アップロード
                    $fileName = Str::uuid()->toString() . '.jpg'; // 'farms/' を削除
                    Storage::disk('s3')->putFileAs('farms', $imageFile, $fileName, 'public'); // 第一引数はfarmsをそのままに
                    $url = Storage::disk('s3')->url('farms/' . $fileName); // URL生成時にパスを指定
    
                    // データベースに画像の情報を登録
                    FarmImage::create([
                        'farm_id' => $farm->id,
                        'image_path' => $url,
                        'image_order' => $index + 1,
                    ]);
                }
            }
        }
    
        return redirect()->route('admin.backend.farms.images', $farm->id)->with('success', '画像が正常にアップロードされました。');
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
        
        // ファイルがアップロードされたか確認
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');

            // 既存の画像をS3から削除
            $existingImagePath = parse_url($image->image_path, PHP_URL_PATH);
            Storage::disk('s3')->delete($existingImagePath);

            // 新しい画像をS3にアップロード
            $fileName = Str::uuid()->toString() . '.jpg';
            Storage::disk('s3')->putFileAs('farms', $imageFile, $fileName, 'public');
            $url = Storage::disk('s3')->url('farms/' . $fileName); // URL生成時のパスを適切に指定

            // データベースレコードを更新
            $image->update([
                'image_path' => $url,
            ]);

            return redirect()->route('admin.backend.farms.editImages', ['farmId' => $farmId])->with('success', '画像が更新されました。');
        }

        return back()->withInput()->withErrors(['error' => '画像の更新に失敗しました。']);
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
