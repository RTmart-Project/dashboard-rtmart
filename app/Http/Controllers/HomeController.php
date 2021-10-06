<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function home()
    {
        $userName = Auth::user();
        return view('home.index', [
            'userName' => $userName
        ]);
    }
}