<?php
//Define Route here for this application.

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FeedbackController;
use Core\Router;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\RegisterController;


Router::get('/',[HomeController::class,'index']);
Router::get('/login',[LoginController::class,'index'])->middleware('guest');
Router::post('/login',[LoginController::class,'store'])->middleware('guest');
Router::get('/register',[RegisterController::class,'index'])->middleware('guest');
Router::post('/register',[RegisterController::class,'store'])->middleware('guest');

//Auth Router
Router::get('/dashboard',[DashboardController::class,'index'])->middleware('auth');

//Feedback Routes
Router::get('/{uuid}',[FeedbackController::class,'index']);
Router::post('/feedback/{uuid}',[FeedbackController::class,'store']);
Router::get('/feedback/success',[FeedbackController::class,'success']);