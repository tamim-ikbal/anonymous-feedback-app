<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use Core\Auth;
use Core\Request;

class HomeController extends Controller{

    public function index(){
        return view('index');
    }

}