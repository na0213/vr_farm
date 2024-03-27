<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Farm;
use App\Models\Point;

class PointController extends Controller
{
    public function create($farmId)
    {
        $farm = Farm::with('owner')->findOrFail($farmId); // Ownerの情報も一緒に取得
        $owner = $farm->owner; // $farmに紐づく$ownerを取得
        return view('backend.points.create', compact('farm', 'owner'));
    }

    public function store(Request $request, $farmId)
    {
        $request->validate([
            'point_name' => 'required|string|max:255',
            'point_info' => 'nullable|string',
        ]);
    
        try {
            DB::beginTransaction();
    
            $point = new Point;
            $point->farm_id = $farmId;
            $point->point_name = $request->input('point_name');
            $point->point_info = $request->input('point_info');
            $point->sdgs = json_encode($request->input('sdgs')); // JSON形式にエンコード
            $point->save();
        
            DB::commit();
            return redirect()->route('admin.backend.points.create', ['farm' => $farmId])->with('success', '登録されました。');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        }
    }

    public function edit($id)
    {
        $point = Point::with('farm.owner')->findOrFail($id);
        $owner = optional($point->farm)->owner;
        // $point = Point::findOrFail($id);
        return view('backend.points.edit', compact('point', 'owner'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'point_name' => 'required|string|max:255',
            'point_info' => 'nullable|string',
        ]);
    
        try {
            DB::beginTransaction();
    
            $point = Point::findOrFail($id);
            $point->point_name = $request->input('point_name');
            $point->point_info = $request->input('point_info');
            $point->sdgs = json_encode($request->input('sdgs'));
            $point->save();
    
            DB::commit();
            return redirect()->route('admin.backend.points.create', ['farm' => $id])->with('success', '更新されました。');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }
    
}
