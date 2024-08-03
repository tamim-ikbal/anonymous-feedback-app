<?php

namespace Core;

defined('ABSPATH') || die('You are not allowed');

class Session{
    protected static $name = 'feedback-app-session';
    protected static $session_life = 3600 * 2;
    protected static $flash_key = 'feedback_app_session_flash';

    public static function start()
    {
        if(!session_id()){
            session_name(static::$name);
            session_set_cookie_params(static::$session_life);
            session_start();
        }
    }

    public static function put($key,$value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key,$default=null)
    {
        return $_SESSION[static::$flash_key][$key] ?? $_SESSION[$key] ?? $default;
    }

    public static function flash($key,$value)
    {
        $_SESSION[static::$flash_key][$key] = $value;
    }

    public static function cleanup()
    {
        unset($_SESSION[static::$flash_key]);
    }

    public static function regenerate()
    {
        static::destroy();
        static::start();
    }


    public static function destroy()
    {
        session_destroy();

        //Expire Cookie
        $cookie_params = session_get_cookie_params();
        setcookie(static::$name,'',time() - 172800,$cookie_params['path'],$cookie_params['domain'],$cookie_params['secure'],$cookie_params['httponly']);
    }
    
}