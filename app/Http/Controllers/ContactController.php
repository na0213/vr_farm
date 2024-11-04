<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use App\Mail\Contact;
use Mail;

class ContactController extends Controller
{
    public function formTop()
    {
        return view('contact.index');
    }

    public function confirm(Request $request)
    {
        $this->validate($request, [
            'send_name'  => 'required',
            'send_email' => 'required|email',  // send_mail を send_email に変更
            'send_message' => 'required',      // message を send_message に変更し、必須化
        ]);

        $postarr = $request->all();
        //present フィールドが入力データに存在していることをバリデート

        return view('contact.confirm', compact('postarr'));
    }

    public function SendProcess(Request $request)
    {
        $action = $request->get('action', 'back'); // submitまたはback
        $input = $request->except('action'); // ボタンのaction部分を除外

        if($action === 'submit') {
            // 入力データの取得
            $postarr = $request->all();
            $mailto = ['farm360.info@gmail.com', $postarr["send_email"]];
            Mail::to($mailto)->send(new Contact($postarr)); // メール送信の処理
            $request->session()->regenerateToken();
            
            return view('contact.complete'); // 完了ビューの表示
        } else {
            // 入力画面への戻り
            return redirect()->action([ContactController::class, 'formTop'])->withInput($input);
        }
    }
    // public function SendProcess(Request $request)
    // {
    //     $action = $request->get('action', 'back');
    //     $input = $request->except('action');


    //     if($action === 'submit') {
    //         $postarr = $request->all();
    //         $mailto = array('farm360.info@gmail.com',$postarr["send_email"]);
    //         Mail::to($mailto)->send(new Contact($postarr));//mailableクラス
    //         $request->session()->regenerateToken();
    //         return view('contact.complete');
    //     } else {
    //         return redirect()->action([ContactController::class, 'formTop'])->withInput($input);
    // }
    // }
    
}
