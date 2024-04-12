<?php

namespace App\Http\Controllers;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index() {
        $shop = Shop::find(Auth::guard('shops')->id());

        return view('dashboard', compact('shop'));
    }
}
