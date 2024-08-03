<?php

namespace Core;

class Template{

    public static function render($view,$data=[])
    {
        $compiledView = ABSPATH.'bootstrap/cache/'.$view.'.php';

        // if(!file_exists($view)){
        //     $content = compile($path);
        //     file_put_contents($view,$content);
        // }

        $content = static::compile($view);
        file_put_contents($compiledView,$content);

        ob_start();
        extract($data);
        require $compiledView;
        return ob_get_clean();
    }

    protected static function compile($view)
    {
        $completeName = $view.'.view.php';
        $blade = ABSPATH.'view/'.$completeName;
        if(!file_exists($blade)){
            throw new \Exception('View '.$completeName.' not found!');
        }
        $bladeContent = file_get_contents($blade);

        //Includes
        $bladeContent = static::includes($bladeContent);

        //Extends
        $extendContent = static::extends($bladeContent);
        
        //Work For Blade Directives
        $contentPattern = '#@(section)\([\',"](\w+)[\',"]\)(.+?)@endsection#s';
        preg_match_all($contentPattern,$bladeContent,$bladeDirectives,PREG_SET_ORDER);
        //dd($bladeDirectives);
        foreach ($bladeDirectives as $bladeDirective) {
            $directive = $bladeDirective[1] ?? '';
            $id = $bladeDirective[2] ?? '';
            $data = $bladeDirective[3] ?? '';
            $extendPattern= '#@show\([\',"]('.$id.')[\',"]\)#';
            $extendContent = preg_replace($extendPattern,$data,$extendContent);
        }


        return $extendContent;

    }

    public static function includes($bladeContent)
    {
        //Includes
        $has_include = preg_match_all('#@includes\(\'([a-z,A-Z,0-9,_,-,/,\.]+)\'\)#',$bladeContent,$includes,PREG_SET_ORDER);
        if($has_include){
            //dd($includes);
            foreach($includes as $include){
                $includePath = $include[1];
                $includeContent = static::compile($includePath);
                $bladeContent = preg_replace('#@includes\(\'('.$includePath.')\'\)#',$includeContent,$bladeContent);
            }
        }
        return trim($bladeContent,'');
    }

    public static function extends($bladeContent)
    {
        $extendContent = $bladeContent;
        preg_match('#@extends\(\'([a-z,A-Z,0-9,_,-,/,\.]+)\'\)#',$bladeContent,$extendView);
        if(count($extendView) > 1){
            $extendContent = static::compile($extendView[1]);
            $extendContent = static::includes($extendContent);
        }

        return trim($extendContent,' ');
    }

}