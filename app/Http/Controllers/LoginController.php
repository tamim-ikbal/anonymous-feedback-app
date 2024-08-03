<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Core\Auth;
use Core\Request;
use Core\Validator;

class LoginController extends Controller{
    public function index()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $validate = Validator::make($request,[
            'email'             => ['required','email','escape'],
            'password'          => ['required','string','escape']
        ]);

        //Early Return If Validation failed
        if(!$validate->valiated()){
            return redirect('/login',[
                'errors' => $validate->errors()
            ]);
        }

        $data = $validate->data();

        $login = Auth::attemp($data['email'],$data['password']);

        if(!$login){
            return redirect('/login',[
                'errors' => [
                    'email' => 'Invalid Crdential.'
                ]
            ]);
        }

        return redirect('/dashboard');
    }
}