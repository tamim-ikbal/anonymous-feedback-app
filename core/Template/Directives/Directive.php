<?php

namespace Core\Template\Directives;

abstract class Directive{
    abstract public static function render($id,$data,$extendContent);

    public static function directives()
    {
        return [
            'section' => SectionDirective::class,
            'push' => PushDirective::class,
            //'if' => IfDirective::class,
            //'{{' => EchoDirective::class,
        ];
    }
}