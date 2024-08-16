<?php

namespace Core\Template\Directives;

class PushDirective extends Directive{

    public static function render($id,$data,$extendContent)
    {
        $extendPattern= '#@(yeild|show)\([\',"]('.$id.')[\',"]\)((.+?)?@end(yeild|show))?#s';
        return preg_replace($extendPattern,$data,$extendContent);
    }
}