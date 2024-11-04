<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use App\Models\Farm;
use App\Models\Post;
use App\Models\Article;

class GuestController extends Controller
{
    public function top()
    {
        $articles = Article::where('is_published', 1)->get();
        return view('home', compact('articles'));
    }
    

    public function index()
    {
        $farms = Farm::where('is_published', 1)->with(['keywords', 'farmImages'])->get();

        return view('farm.map', compact('farms'));
    }

    public function show($id)
    {
        // 関連するfarmとarticlesを取得
        $farm = Farm::with('animals')->findOrFail($id);
        $articles = Article::where('farm_id', $id)->where('is_published', true)->get(); // farm_idに一致する公開済みの記事を取得
        
        return view('farm.show', compact('farm', 'articles'));
    }

    // コミュニティ
    public function communityIndex(Farm $farm)
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
    
        $farmImages = $farm->farmImages()->orderBy('image_order')->get();

        return view('farm.community', compact('farm', 'allPosts', 'farmImages'));
    }

    public function showArticle($id)
    {
        // 記事のIDで記事を検索
        $article = Article::findOrFail($id);

        // 記事詳細ビューにデータを渡して表示
        return view('article.show', compact('article'));
    }
    
}
