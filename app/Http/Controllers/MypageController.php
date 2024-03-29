<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Mypage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MypageController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $mypage = $user->mypage;

        if ($mypage) {
            // Mypageが存在する場合は編集ページへリダイレクト
            return redirect()->route('user.mypage.edit', $mypage->id);
        } else {
            // Mypageが存在しない場合は新規作成ページへリダイレクト
            return redirect()->route('user.mypage.create');
        }
    }

    public function create()
    {
        return view('mypage.create');
    }
    
    public function edit($id)
    {
        $mypage = Mypage::findOrFail($id);
        return view('user.mypage.edit', compact('mypage'));
    }

    public function update(Request $request, Mypage $mypage)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:255',
            'catchphrase' => 'nullable|string',
            'my_image' => 'nullable|image|max:3072', // 3MBまで
            'is_published' => 'required|boolean',
        ]);
    
        try {
            DB::beginTransaction();
    
            $mypage->nickname = $validated['nickname'];
            $mypage->catchphrase = $validated['catchphrase'];
            $mypage->is_published = $validated['is_published'];
    
            // 画像の処理
            if ($request->hasFile('my_image')) {
                // 既存の画像をS3から削除
                if ($mypage->my_image) {
                    $existingImagePath = parse_url($mypage->my_image, PHP_URL_PATH);
                    Storage::disk('s3')->delete($existingImagePath);
                }

                // 新しい画像を処理
                $image = $request->file('my_image');
                $fileName = 'my_images/' . uniqid() . '.jpg';

                // S3にアップロード
                Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                $url = Storage::disk('s3')->url($fileName);

                // データベースを更新
                $mypage->my_image = $url;
            }
    
            $mypage->save();
    
            DB::commit();
    
            return redirect()->route('user.mypage.index')
                ->with('message', '情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }
    

    public function show(User $user)
    {
        $mypage = $user->mypage;

        if (!$mypage || !$mypage->is_published) {
            abort(404, 'ページが見つかりません。');
        }

        return view('mypage.show', compact('mypage'));
    }
}
