<?php

namespace Core;

defined('ABSPATH') || die('You are not allowed');

class Auth{
    protected static ?array $user = null;
    protected static string $auth_key = 'auth_id';

    public static function user()
    {
        return static::$user;
    }
    
    public static function id()
    {
        return static::$user ? static::$user['id'] : null;
    }

    public static function check()
    {
        return static::$user ? true : false;
    }

    public static function attemp($email,$password,$remember_me=false)
    {
        $user = static::verifyCredential($email,$password);

        if(!$user){
            return false;
        }

        static::login($user);

        return true;

    }

    public static function login(array $user)
    {
        //Have to fix Regenarte Issue
        //Session::regenerate();

        Session::put(static::$auth_key,$user['id']);

        if($user){
            unset($user['password']);
        }

        static::$user = $user;

    }

    protected static function verifyCredential($email,$password)
    {
        $user = DB::table('users')->where('email','=',$email)->first();
        if(!$user){
            return null;
        }

        //Password Check
        if(!password_verify($password,$user['password'])){
            return null;
        }

        return $user;

    }

    public static function make()
    {
        $user = null;

        $authSession = Session::get(static::$auth_key,null);

        if($authSession){
            $user = DB::table('users')->where('id','=',$authSession)->first();
            if($user){
                unset($user['password']);
            }
        }

        static::$user = $user;
    }
}