<?php

//Autoloader By Tamim

spl_autoload_register(function($class_name){
    $class_path = ABSPATH.str_replace(['App','Core','\\'],['app','core',DIRECTORY_SEPARATOR],$class_name).'.php';
    require_once $class_path;
});