<?php

namespace App\Http\Middlewares;

use Core\Auth;
use App\Contracts\Middleware;

class AuthMiddleware implements Middleware{

    public function handle()
    {
        if(!Auth::check()){
            return redirect('/login');
        }
    }

}