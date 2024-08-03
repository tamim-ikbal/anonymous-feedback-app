<?php

namespace App\Traits;

trait Singleton{
    static $instance = null;

    public static function getInstance() {
        if(null === static::$instance){
            static::$instance = new Static();
        }
        return static::$instance;
    }
}