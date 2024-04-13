<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Note;

class UserController extends Controller
{
    public function top()
    {
        try {
            $query = '(アニマルウェルフェア OR 循環型酪農 OR 循環型畜産 OR グラスフェッド) OR (牛乳 AND 放牧)';
            $url = config('newsapi.news_api_url') . "everything?q={$query}&apiKey=" . config('newsapi.news_api_key');
            $method = "GET";
    
            $client = new \GuzzleHttp\Client();
            $response = $client->request($method, $url);
    
            $results = $response->getBody();
            $articles = json_decode($results, true);
    
            $news = [];
    
            foreach ($articles['articles'] as $article) {
                array_push($news, [
                    'name' => $article['title'],
                    'url' => $article['url'],
                    'thumbnail' => $article['urlToImage'],
                ]);
            }

            $notes = Note::with('mypage')->get();

        } catch (\GuzzleHttp\Exception\RequestException $e) {
            echo \GuzzleHttp\Psr7\Message::toString($e->getRequest());
            if ($e->hasResponse()) {
                echo \GuzzleHttp\Psr7\Message::toString($e->getResponse());
            }
            $news = [];
            $notes = [];
        }
    
        return view('dashboard', compact('news', 'notes'));
    }

    public function index()
    {
        $farms = Farm::where('is_published', 1)->with(['keywords', 'farmImages'])->get();

        return view('user.farm.map', compact('farms'));
    }
    
    public function show($id)
    {
        $farm = Farm::with('animals')->findOrFail($id);
        return view('user.farm.show', compact('farm'));
    }

    // お気に入り登録
    public function toggleFavorite(Request $request, Farm $farm)
    {
        $user = $request->user();
    
        $like = $farm->likes()->where('user_id', $user->id)->first();
    
        if ($like) {
            // すでにお気に入りなら削除する
            $like->delete();
            $isFavorite = false;
        } else {
            // お気に入りになければ追加する
            $farm->likes()->create(['user_id' => $user->id]);
            $isFavorite = true;
        }
    
        $favoritesCount = $farm->likes()->count();
    
        return response()->json([
            'isFavorite' => $isFavorite,
            'favoritesCount' => $favoritesCount
        ]);
    }

    // お気に入り一覧
    public function favorites(Request $request)
    {
        $user = $request->user();
        $favorites = $user->likes()->with('farm')->get()->map(function ($like) {
            return $like->farm;
        });
    
        // マイページが存在しない場合は空のコレクションを返す
        $followings = collect();
    
        if ($user->mypage) {
            // フォローしているマイページを取得
            $followings = $user->mypage->following()->with('followed')->get()->map(function ($follow) {
                return $follow->followed;
            });
        }
    
        return view('user.favorites', compact('favorites', 'followings'));
    }
    
}
