<?php 

//Session Start

use Core\Auth;
use Core\Router;
use Core\Session;

//Session Start
Session::start();

//Auth Handler
Auth::make();

//Router
require_once ABSPATH.'router/web.php';

//Reslove Routes
Router::resolve();

//Cleanup Session: Delete Flash session etc.
Session::cleanup();