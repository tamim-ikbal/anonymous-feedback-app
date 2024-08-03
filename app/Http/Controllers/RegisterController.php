<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Services\FeedbackLinkService;
use Core\Auth;
use Core\DB;

class RegisterController extends Controller{

    public function index()
    {
        return view('register');
    }

    public function store(RegisterRequest $request)
    {

        $data = $request->validated();

        $userDTO = [
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => password_hash($data['password'],PASSWORD_BCRYPT),
        ];

        $user = DB::table('users')->where('email','=',$userDTO['email'])->first();

        if($user){
            return redirect('/register',[
                'errors' => [
                    'email' => 'Email already exists!'
                ]
            ]);
        }

        $user_id = DB::table('users')->insert($userDTO);

        if(!$user_id){
            return redirect('/register',[
                'errors' => [
                    'email' => 'Something went wriong!'
                ]
            ]);
        }

        //Generate Link
        FeedbackLinkService::createLink($user_id);

        Auth::attemp($data['email'],$data['password']);

        return redirect('/');
    }
}