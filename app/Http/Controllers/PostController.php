<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Post;

class PostController extends Controller
{
    public function index(Farm $farm)
    {
        // 特定のファームに関連する生産者（owner）の投稿を取得
        $ownerposts = $farm->ownerposts()->get();

        // 特定のファームに関連するすべてのユーザーの投稿を取得
        $posts = Post::with('mypage')->where('farm_id', $farm->id)->get();

        return view('user.community.index', compact('farm', 'ownerposts', 'posts'));
    }
    // public function index(Farm $farm)
    // {
    //     // 特定のファームに関連する投稿を取得
    //     $posts = $farm->posts()->with('mypage')->get();

    //     // 現在のユーザーのMypage情報を取得
    //     $mypage = auth()->user()->mypage ?? null;

    //     return view('user.community.index', compact('farm', 'mypage', 'posts'));
    // }

    public function store(Request $request)
    {
        $request->validate([
            'farm_id' => 'required|exists:farms,id',
            'post_title' => 'nullable|string|max:255',
            'post_content' => 'nullable|string',
        ]);
    
        $user = auth()->user();
    
        // ユーザーに紐づくMypageを取得、存在しなければ作成
        $mypage = $user->mypage;
    
        if (!$mypage) {
            $mypage = $user->mypage()->create([
                'nickname' => '名無しさん',  // 新規作成時にデフォルトで「名無しさん」を設定
                // その他のMypageに必要なデフォルト値
            ]);
        } elseif (empty($mypage->nickname)) {
            $mypage->nickname = '名無しさん';
            $mypage->save();
        }
    
        $post = new Post();
        $post->mypage_id = $mypage->id;
        $post->farm_id = $request->farm_id;
        $post->post_title = $request->post_title;
        $post->post_content = $request->post_content;
        $post->save();
    
        return redirect()->route('user.community.index', ['farm' => $post->farm_id])
        ->with('success', '投稿が成功しました。');
    }
    
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        // 必要に応じて、削除前にユーザーの権限チェックなどを行う
        $post->delete();

        return redirect()->route('user.mypage.show')->with('success', '投稿が削除されました。');
    }
    
}
