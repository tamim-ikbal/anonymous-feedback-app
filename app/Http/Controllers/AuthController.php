<?php

namespace App\Http\Controllers;

use Core\Request;

class AuthController extends Controller{

    public function loginView()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        dd($request);
    }

    public function logout(Request $request)
    {
        dd($request);
    }

}