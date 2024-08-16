<?php

use Core\Auth;
use Core\Router;
use Core\Session;
use Core\Template\Template;

defined('ABSPATH') || die('You are not allowed');

//BasePath
if(!function_exists('base_path')){
    function base_path($path){
        return ABSPATH.ltrim($path,'/');
    }
}


//Public Path
if(!function_exists('public_path')){
    function public_path($path){
        return base_path('public/'.ltrim($path,'/'));
    }
}


//Public Path
if(!function_exists('asset')){
    function asset($path){
        return SITE_URL . '/'.ltrim($path,'/');
    }
}



//Render View
if(!function_exists('view')){
    function view($name, $data=[]) {
        // ob_start();
        // extract($data);
        // require(ABSPATH.'view/'.$name.'.view.php');
        // return ob_get_clean();
        return Template::render($name,$data);
    }
}



//Dump And Die
if(!function_exists('dd')){
    function dd($data,$die=true)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
        if($die){
            die();
        }
    }
}


if(!function_exists('uri')){
    function uri($path){
        return SITE_URL.'/'.ltrim($path,'/');
    }
}


if(!function_exists('redirect')){
    function redirect($to='/',$with=[]){
        if(count($with) > 0){
            foreach($with as $key => $value){
                Session::flash($key,$value);
            }
        }
        header('Location: /'.ltrim($to,'/'));
        exit();
    }
}


if(!function_exists('auth')){
    function auth(){
        return Auth::class;
    }
}

if(!function_exists('has_error')){
    function has_error($name){
        return isset(Session::get('errors')[$name]);
    }
}

//Display Form Errors
if(!function_exists('display_error')){
    function display_error($name){
        $error = Session::get('errors')[$name] ?? '';
        if(is_array($error)){
            return $error[0] ?? '';
        }
        return $error;    
    }
}

//Display Form Old Data
if(!function_exists('old')){
    function old($name,$default=''){
        $old = Session::get('old')[$name] ?? $default;
        if(is_array($old)){
            return $old[0] ?? '';
        }
        return $old;    
    }
}

//Abort
if(!function_exists('abort')){
    function abort($status_code=404)
    {
        Router::abort(function() use ($status_code){
            switch($status_code){
                case 500:
                    return view('errors/500');
                    break;
                default:
                    return view('errors/404');
                    break;
            }
        },$status_code);
    }
}

if(!function_exists('back')){
    function back()
    {
        $prevUrl = parse_url($_SERVER['HTTP_REFERER'] ?? '');
        $url = $prevUrl['path'].(isset($prevUrl['query']) ? '?'.$prevUrl['query']:'');
        return redirect($url);
    }
}