<?php

namespace Core;

defined('ABSPATH') || die('You are not allowed');

use App\Http\Middlewares\Middleware;
use Core\Request;

/**
 * Name: PHP Router 
 * Description: PHP Router is inspired by Laravel And Hasin Haydar
 * Author: Tamim
 */

class Router{

    protected static $routes = [];

    public static function get($path,$callback)
    {
        return static::add($path,$callback,'GET');
    }

    public static function post($path,$callback)
    {
        return static::add($path,$callback,'POST');
    }

    public static function delete($path,$callback)
    {
        return static::add($path,$callback,'delete');
    }

    public static function put($path,$callback)
    {
        return static::add($path,$callback,'PUT');
    }

    public static function patch($path,$callback)
    {
        return static::add($path,$callback,'patch');
    }

    public function middleware($middleware)
    {
        static::$routes[array_key_last(static::$routes)]['middleware'] = $middleware;

        return new Static();
    }
    
    public function name($name)
    {
        static::$routes[array_key_last(static::$routes)]['name'] = $name;
        
        return new Static();
    }

    protected static function add($path,$callback,$request_method = 'GET')
    {
        static::$routes[] = [
            'path' => $path,
            'callback' => $callback,
            'method' => $request_method,
            'middleware' => null,
            'name' => null,
        ];

        return new Static();
    }

    public static function abort($callback,$status_code=404)
    {
        http_response_code($status_code);
        $params = static::getParams($callback,[]);        
        echo static::call_user_func($callback,$params);
        exit;
    }

    public static function resolve(){
        $notfound = true;
        $request_uri = $_SERVER['REQUEST_URI'];
        $request_method = $_SERVER['REQUEST_METHOD'];

        foreach (static::$routes as $route){
            [$match,$matches] = static::matchPathAndUri($route['path'],$request_uri);
            if($match && $request_method === $route['method']){

                //Middleware
                if($route['middleware']){
                    Middleware::resolve($route['middleware']);
                }

                $params = static::getParams($route['callback'],$matches);
                echo static::call_user_func($route['callback'],$params);
                
                $notfound = false;
                break;
            }
        }

        if($notfound){
            abort(404);
        }
        
    }

    private static function call_user_func($callback,array $params)
    {
        $output = '';
        if(is_array($callback)){
            $class = $callback[0];
            $method = $callback[1];
            $output = $class::getInstance()->$method(...$params);
        }elseif(is_callable($callback)){
            $output = call_user_func($callback,...$params);
        }
        return $output;
    }

    private static function getParams($callback,array $matches):array
    {
        $reflector = null;
        if(is_array($callback)){
            $reflector = new \ReflectionMethod($callback[0],$callback[1]);
        }else{
            $reflector = new \ReflectionFunction($callback);
        }

        
        //Do Early Return
        if(null === $reflector){
            return [];
        }

        // START: This code is written by ChatGPT
        $params = $reflector->getParameters();
        $getRequestClass = isset($params[0]) ? $params[0]->getType()?->getName() ?? '' : '';
        if (Request::class === $getRequestClass || is_subclass_of($getRequestClass,Request::class)) {
            // Add Request object as the first parameter
            array_unshift($matches, new $getRequestClass());
        }
        return $matches;
    }

    private static function matchPathAndUri($path,$uri) 
    {
        
        $path = '/'.trim($path,'/');

        //Build RGX
        $path = preg_replace('/\{\w+\}/','(\w+)',$path);
        $regx = '#^'.$path.'/?$#';

        $uri = parse_url(str_replace('//','/',$uri))['path'];
        
        
        $match = preg_match($regx,$uri,$matches);

        //Filter Macthes
        $matches = array_filter($matches,function ($item){
            return strpos($item,'/') === FALSE;
        });

        return [
            $match,$matches
        ];
    }

    public static function prevUrl()
    {
       $prevUrl =  $_SESSION['HTTP_REFFER'];
    }

}