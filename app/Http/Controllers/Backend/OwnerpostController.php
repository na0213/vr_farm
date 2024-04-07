<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Ownerpost;
use App\Models\Mypage;
use App\Models\Owner;

class OwnerpostController extends Controller
{
    public function store(Request $request)
    {
        try {
            $owner = auth()->guard('owners')->user();
            $farm = $owner->farm;
    
            $request->validate([
                'post_title' => 'nullable|string',
                'post_content' => 'nullable|string',
            ]);
    
            $ownerpost = new Ownerpost();
            $ownerpost->farm_id = $farm->id;
            $ownerpost->post_title = $request->post_title;
            $ownerpost->post_content = $request->post_content;
            $ownerpost->save();
    
            return back()->with('success', '投稿が保存されました。');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->withErrors('投稿の保存中にエラーが発生しました。');
        }
    }

    public function destroy($id)
    {
        try {
            $ownerpost = Ownerpost::findOrFail($id);
            $owner = auth()->guard('owners')->user();
            
            // 生産者がその投稿を所有していることを確認
            if ($ownerpost->farm_id != $owner->farm->id) {
                return back()->withErrors('不正な操作が検出されました。');
            }

            $ownerpost->delete();
            return back()->with('success', '投稿が削除されました。');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->withErrors('投稿の削除中にエラーが発生しました。');
        }
    }

    public function usershow($id)
    {
        $owner = Owner::find(Auth::guard('owners')->id());
        $farm = $owner->farm;
        $mypage = Mypage::where('id', $id)->where('is_published', true)->firstOrFail();
        // Mypageに紐づく投稿を取得
        $posts = $mypage->posts()->get();
        return view('backend.masters.usershow', compact('owner', 'farm', 'mypage', 'posts'));
    }
}
