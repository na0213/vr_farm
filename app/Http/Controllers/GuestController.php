<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7;
use App\Models\Farm;
use App\Models\Post;


class GuestController extends Controller
{
    public function top()
    {
        // try {
        //     $query = '(アニマルウェルフェア OR 循環型酪農 OR 循環型畜産 OR グラスフェッド) OR (牛乳 AND 放牧) OR (畜産 AND SDGs)';
        //     $url = config('newsapi.news_api_url') . "everything?q={$query}&apiKey=" . config('newsapi.news_api_key');
        //     $method = "GET";
    
        //     $client = new \GuzzleHttp\Client();
        //     $response = $client->request($method, $url);
    
        //     $results = $response->getBody();
        //     $articles = json_decode($results, true);
    
        //     $news = [];
    
        //     foreach ($articles['articles'] as $article) {
        //         array_push($news, [
        //             'name' => $article['title'],
        //             'url' => $article['url'],
        //             'thumbnail' => $article['urlToImage'],
        //         ]);
        //     }
        // } catch (\GuzzleHttp\Exception\RequestException $e) {
        //     echo \GuzzleHttp\Psr7\Message::toString($e->getRequest());
        //     if ($e->hasResponse()) {
        //         echo \GuzzleHttp\Psr7\Message::toString($e->getResponse());
        //     }
        // }
    
        return view('home');
    }
    

    public function index()
    {
        $farms = Farm::where('is_published', 1)->with(['keywords', 'farmImages'])->get();

        return view('farm.map', compact('farms'));
    }

    public function show($id)
    {
        $farm = Farm::with('animals')->findOrFail($id);
        return view('farm.show', compact('farm'));
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

}
