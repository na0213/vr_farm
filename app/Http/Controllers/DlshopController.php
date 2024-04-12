<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Farm;
use App\Models\Dlshop;

class DlshopController extends Controller
{
    public function index(Farm $farm)
    {
        $qrs = $farm->qr;
        return view('qrs.index', compact('qrs', 'farm'));
    }

    public function store(Request $request, Farm $farm)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'shop_name' => 'required|string|max:255',
            'email' => 'required|email',
            'purpose' => 'required|string',
        ]);

        $dlshop = new Dlshop([
            'name' => $request->name,
            'shop_name' => $request->shop_name,
            'email' => $request->email,
            'purpose' => $request->purpose,
        ]);
        $farm->dlshops()->save($dlshop);

        return redirect()->route('qrs.show', ['farm' => $farm->id, 'qr' => $farm->qr->id]);
    }

    public function show(Farm $farm)
    {
        $qr = $farm->qr;
        if (!$qr) {
            abort(404, 'QRコードが見つかりません。');
        }
        return view('qrs.show', compact('farm', 'qr'));
    }
}
