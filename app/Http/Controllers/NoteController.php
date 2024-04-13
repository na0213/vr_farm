<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            'note_image' => 'required|image|max:3072', // 3MBまで
        ]);
    
        try {
            // トランザクション開始
            DB::beginTransaction();

            $url = null;
            if ($request->hasFile('note_image')) {
                // ファイルのパスを取得
                $image = $request->file('note_image');

                // ファイル名を生成
                $fileName = 'note_images/' . uniqid() . '.jpg';

                // S3に画像を保存
                Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                $url = Storage::disk('s3')->url($fileName);
            }

            $note = new Note();
            $note->mypage_id = auth()->user()->mypage->id; // ログイン中のユーザーIDを設定
            $note->note_title = $validated['note_title'];
            $note->note_content = $request->input('editor1');
            $note->note_image = $url;
        
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
        // dd($request);
        $validated = $request->validate([
            'note_title' => 'required|string|max:255',
            'note_content' => 'nullable|string|max:3000',
            'note_image' => 'nullable|image|max:3072', // 3MBまで
        ]);
    
        try {
            DB::beginTransaction();
    
            $note = Note::findOrFail($id);
            $note->note_title = $validated['note_title'];
            $note->note_content = $request->input('editor1'); // CKEditor からの入力

            if ($request->hasFile('note_image')) {
                // 既存の画像をS3から削除
                if ($note->note_image) {
                    $existingImagePath = parse_url($note->note_image, PHP_URL_PATH);
                    Storage::disk('s3')->delete($existingImagePath);
                }

                // 新しい画像を処理
                $image = $request->file('note_image');
                $fileName = 'note_images/' . uniqid() . '.jpg';

                // S3にアップロード
                Storage::disk('s3')->put($fileName, file_get_contents($image), 'public');
                $url = Storage::disk('s3')->url($fileName);

                // データベースを更新
                $note->note_image = $url;
            }

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
    
        if ($note->note_image) {
            Storage::disk('s3')->delete(parse_url($note->note_image, PHP_URL_PATH));
        }
        
        // データベースから削除
        $note->delete();
    
        return redirect()->route('user.mypage.show')->with('success', '販売店が削除されました。');
    }

    public function publicShow($id)
    {
        $note = Note::with('mypage')->findOrFail($id);

        return view('user.note.public', compact('note')); 
    }
}
