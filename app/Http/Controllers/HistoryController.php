<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Farm;
use App\Models\Point;
use App\Models\History;

class HistoryController extends Controller
{
    public function index()
    {
        $histories = History::with(['point.farm'])->where('user_id', auth()->id())->get();
        return view('user.history.show', compact('histories'));
    }

    public function show($point_id)
    {
        $point = Point::with('farm')->findOrFail($point_id);
    
        return view('user.history.index', compact('point'));
    }

    public function store(Request $request, $point_id)
    {
        // 既に登録されているか確認
        $alreadyRegistered = History::where('user_id', auth()->id())
            ->where('point_id', $point_id)
            ->exists();
    
        if ($alreadyRegistered) {
            return redirect()->back()->withErrors(['message' => '既に登録されています。']);
        }
    
        $history = new History;
        $history->user_id = auth()->id();
        $history->point_id = $point_id;
        $history->save();
    
        // PointからFarmのIDを取得
        $point = Point::findOrFail($point_id);
        $farmId = $point->farm_id;
    
        // Farmの詳細画面にリダイレクト
        return redirect()->route('user.farm.show', ['id' => $farmId])->with('success', '履歴に登録しました。');
    }

}