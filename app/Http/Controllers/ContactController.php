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
            'send_mail' => 'required|email',
            'message' => 'present',
        ]);
        $postarr = $request->all();
        //present フィールドが入力データに存在していることをバリデート

        return view('contact.confirm', compact('postarr'));
    }

    public function SendProcess(Request $request)
    {
        $action = $request->get('action', 'back');
        $input = $request->except('action');


        if($action === 'submit') {
            $postarr = $request->all();
            $mailto = array('farm360.info@gmail.com',$postarr["send_mail"]);
            Mail::to($mailto)->send(new Contact($postarr));//mailableクラス
            $request->session()->regenerateToken();
            return view('contact.complete');
        } else {
            return redirect()->action([ContactController::class, 'formTop'])->withInput($input);
    }
    }
    
}
