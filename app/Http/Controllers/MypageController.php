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
    public function create()
    {
        return view('user.mypage.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|max:255',
            'catchphrase' => 'nullable|string|max:3000',
            'my_image' => 'nullable|image|max:3072', // 3MBまで
            'is_published' => 'required|boolean',
        ]);
    
        $mypage = new Mypage();
        $mypage->user_id = auth()->id(); // ログイン中のユーザーIDを設定
        $mypage->nickname = $validated['nickname'];
        $mypage->catchphrase = $validated['catchphrase'];
        $mypage->is_published = $validated['is_published'];
    
        // 画像がアップロードされた場合の処理
        if ($request->hasFile('my_image')) {
            $image = $request->file('my_image');

            // ファイル名を生成
            $fileName = 'my_images/' . uniqid() . '.jpg';

            // S3に画像を保存
            Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
            $url = Storage::disk('s3')->url($fileName);

            // $image = $request->file('my_image');
            // $fileName = 'my_images/' . uniqid() . '.' . $image->extension();
            // $path = $image->storeAs(null, $fileName, 'public');
            $mypage->my_image = $url;
        }
    
        $mypage->save();
    
        return redirect()->route('user.mypage.show')->with('success', 'Mypageが登録されました。');
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
    
            return redirect()->route('user.mypage.show')
                ->with('message', '情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }
    
    public function show()
    {
        $user = auth()->user();
        $mypage = $user->mypage;
    
        if (!$mypage) {
            // Mypageがない場合は作成画面にリダイレクト
            return redirect()->route('user.mypage.create');
        }
    
        // Mypageに紐づく投稿を取得
        $posts = $mypage->posts()->get();
        $notes = $mypage->notes()->get();
    
        // Mypageが存在する場合は、詳細画面を表示
        return view('user.mypage.show', compact('mypage', 'posts', 'notes'));
    }

    public function destroy(string $id)
    {
        $mypage = Mypage::findOrFail($id);

        // S3から画像を削除
        if ($mypage->my_image) {
            Storage::disk('s3')->delete(parse_url($mypage->my_image, PHP_URL_PATH));
        }
    
        // データベースから削除
        $mypage->delete();
    
        return redirect()->route('user.mypage.show')->with('success', '販売店が削除されました。');
    }

    public function publicShow($id)
    {
        $mypage = Mypage::where('id', $id)->where('is_published', true)->firstOrFail();
        // Mypageに紐づく投稿を取得
        $posts = $mypage->posts()->get();
        $notes = $mypage->notes()->get();
        return view('user.mypage.public_show', compact('mypage', 'posts', 'notes'));
    }

    public function follow(Request $request, $id)
    {
        $follower = auth()->user()->mypage;
        $followed = Mypage::findOrFail($id);
    
        if ($follower->id !== $followed->id) {
            $follower->following()->updateOrCreate([
                'followed_id' => $followed->id,
            ]);
        }
    
        return back()->with('success', 'フォローしました！');
    }
    public function unfollow($id)
    {
        $follower = auth()->user()->mypage;
        $followed = Mypage::findOrFail($id);
    
        $follower->following()->where('followed_id', $followed->id)->delete();
    
        return back()->with('success', 'フォローを解除しました！');
    }
}
