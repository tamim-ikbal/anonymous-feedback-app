<?php

namespace Core\Template\Directives;

class SectionDirective extends Directive{
    public static function render($id,$data,$extendContent)
    {
        $extendPattern= '#@show\([\',"]('.$id.')[\',"]\)#';
        return preg_replace($extendPattern,$data,$extendContent);
    }
}