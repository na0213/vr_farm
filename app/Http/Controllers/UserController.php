<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;

class UserController extends Controller
{
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
    
        return view('user.favorites', compact('favorites'));
    }
    
}
