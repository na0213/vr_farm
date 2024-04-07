<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Post;

class PostController extends Controller
{
    // public function index(Farm $farm)
    // {
    //     // 特定のファームに関連する生産者（owner）の投稿を取得
    //     $ownerposts = $farm->ownerposts()->get();

    //     // 特定のファームに関連するすべてのユーザーの投稿を取得
    //     $posts = Post::with('mypage')->where('farm_id', $farm->id)->get();

    //     return view('user.community.index', compact('farm', 'ownerposts', 'posts'));
    // }
    public function index(Farm $farm)
    {
        $ownerposts = $farm->ownerposts()->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'is_owner' => true,
                'mypage' => null,
                'created_at' => $post->created_at,
                'image' => $post->owner_image ?? null,
            ];
        })->toArray();
    
        $posts = Post::with('mypage')->where('farm_id', $farm->id)->get()->map(function ($post) {
            return [
                'id' => $post->id,
                'post_title' => $post->post_title,
                'post_content' => $post->post_content,
                'is_owner' => false,
                'mypage' => $post->mypage,
                'created_at' => $post->created_at,
                'image' => optional($post->mypage)->my_image ?? null,
            ];
        })->toArray();
    
        $allPosts = array_merge($ownerposts, $posts);
        usort($allPosts, function ($a, $b) {
            return $a['created_at'] <=> $b['created_at'];
        });
        $mypage = auth()->user()->mypage;

        $farmImages = $farm->farmImages()->orderBy('image_order')->get();

        return view('user.community.index', compact('farm', 'allPosts', 'mypage', 'farmImages'));
    }
    

    // public function index(Farm $farm)
    // {
    //     // 特定のファームに関連する生産者（owner）の投稿を取得
    //     $ownerposts = $farm->ownerposts()->get();
    
    //     // 特定のファームに関連するすべてのユーザーの投稿を取得
    //     $posts = Post::with('mypage')->where('farm_id', $farm->id)->get();
    
    //     // 現在ログインしているユーザーのMypage情報を取得
    //     $mypage = auth()->user()->mypage;
    
    //     return view('user.community.index', compact('farm', 'ownerposts', 'posts', 'mypage'));
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
