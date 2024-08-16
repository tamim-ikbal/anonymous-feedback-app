<?php

namespace Core\Template\Directives;

class IfDirective extends Directive{
    public static function render($id,$data,$extendContent)
    {
        dd($data);
        $extendPattern= '#@show\([\',"]('.$id.')[\',"]\)#';
        return preg_replace($extendPattern,$data,$extendContent);
    }
}