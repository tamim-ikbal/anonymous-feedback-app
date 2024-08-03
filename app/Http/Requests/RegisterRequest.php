<?php

namespace App\Http\Requests;

use Core\Request;

class RegisterRequest extends Request{

    protected function rules():array
    {
        return [
            'name'              => ['required','string','escape'],
            'email'             => ['required','email','escape'],
            'password'          => ['required','string','same:confirm_password','escape'],
            'confirm_password'  =>  ['required','string','escape'],
        ];
    }
}