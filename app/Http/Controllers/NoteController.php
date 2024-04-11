<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NoteController extends Controller
{
    public function create()
    {
        return view('user.note.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'note_title' => 'required|string|max:255',
            'note_content' => 'nullable|string|max:3000',
        ]);
    
        try {
            // トランザクション開始
            DB::beginTransaction();

            $note = new Note();
            $note->mypage_id = auth()->user()->mypage->id; // ログイン中のユーザーIDを設定
            $note->note_title = $validated['note_title'];
            // $note->note_content = $validated['note_content'];
            $note->note_content = $request->input('editor1');
        
            $note->save();

            DB::commit();

            return redirect()->route('user.note.create')->with('success', 'Mypageが登録されました。');

        } catch (\Exception $e) {
        // エラーが発生した場合はロールバック
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '保存に失敗しました。']);
        };
    }

    public function show($note)
    {
        $note = Note::findOrFail($note); // $note はノートのID
    
        return view('user.note.show', compact('note'));
    }

    public function edit($id)
    {
        $note = Note::findOrFail($id);
        return view('user.note.edit', compact('note'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'note_title' => 'required|string|max:255',
            'note_content' => 'nullable|string|max:3000',
        ]);
    
        try {
            DB::beginTransaction();
    
            $note = Note::findOrFail($id);
            $note->note_title = $validated['note_title'];
            $note->note_content = $request->input('editor1'); // CKEditor からの入力
            $note->save();
    
            DB::commit();
    
            return redirect()->route('user.note.show', ['id' => $note->id])
                ->with('message', '牧場の情報が更新されました');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            return back()->withInput()->withErrors(['error' => '更新に失敗しました。']);
        }
    }

    public function destroy($id)
    {
        $note = Note::findOrFail($id);
    
        // データベースから削除
        $note->delete();
    
        return redirect()->route('user.mypage.show')->with('success', '販売店が削除されました。');
    }
}
