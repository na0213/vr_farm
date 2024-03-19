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
}
