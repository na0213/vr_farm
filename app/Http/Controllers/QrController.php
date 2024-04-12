<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Farm;
use App\Models\Qr;

class QrController extends Controller
{
    public function create($farmId)
    {
        $farm = Farm::with('owner')->findOrFail($farmId); // Ownerの情報も一緒に取得
        $owner = $farm->owner; // $farmに紐づく$ownerを取得
        return view('backend.qr.create', compact('farm', 'owner'));
    }

    public function store(Request $request, $farmId)
    {
        $request->validate([
            'image_path' => 'required|image|max:3072',
        ]);
    
        try {
            DB::beginTransaction();

            $url = null;
            if ($request->hasFile('image_path')) {
                // ファイルのパスを取得
                $image = $request->file('image_path');

                // ファイル名を生成
                $fileName = 'qr/' . uniqid() . '.jpg';

                // S3に画像を保存
                Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                $url = Storage::disk('s3')->url($fileName);
            }

            $qr = new Qr;
            $qr->farm_id = $farmId;
            $qr->image_path = $url;
            $qr->save();
        
            DB::commit();
            return redirect()->route('admin.backend.qr.create', ['farm' => $farmId])->with('success', '登録されました。');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }
    }

    public function edit($id)
    {
        $qr = Qr::with('farm.owner')->findOrFail($id);
        $owner = optional($qr->farm)->owner;
        return view('backend.qr.edit', compact('qr', 'owner'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'image_path' => 'nullable|image|max:3072',
        ]);
    
        try {
            DB::beginTransaction();
    
            $qr = Qr::findOrFail($id);
            if ($request->hasFile('image_path')) {
                // 既存の画像をS3から削除
                if ($qr->image_path) {
                    $existingImagePath = parse_url($qr->image_path, PHP_URL_PATH);
                    Storage::disk('s3')->delete($existingImagePath);
                }
    
                $image = $request->file('image_path');
                // S3にアップロード
                $fileName = 'qr/' . uniqid() . '.jpg';
                Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                $url = Storage::disk('s3')->url($fileName);
    
                // データベースを更新
                $qr->image_path = $url;
            }

            $qr->save();
    
            DB::commit();
            return redirect()->route('admin.backend.qr.create', ['farm' => $id])->with('success', '更新されました。');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function destroy(string $id)
    {
        $qr = Qr::findOrFail($id);
        $owner = $qr->farm->owner; // $farmに紐づく$ownerを取得
        // S3から画像を削除
        if ($qr->image_path) {
            Storage::disk('s3')->delete(parse_url($qr->image_path, PHP_URL_PATH));
        }
    
        // データベースから削除
        $qr->delete();
    
        return redirect()->route('admin.backend.qr.create', ['farm' => $id])->with('success', '販売店が削除されました。');
    }
}
