<?php

namespace App\Http\Controllers;

class ContactController extends Controller
{
    // 入力・確認・完了は1ページの中で切り替え、送信は Cloudflare の Worker が受け取る
    public function formTop()
    {
        return view('contact.index');
    }
}
