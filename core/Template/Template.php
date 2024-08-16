<?php

namespace Core\Template;

use Core\Template\Directives\Directive;

class Template{

    public static function render($view,$data=[])
    {
        $compiledView = ABSPATH.'bootstrap/cache/'.$view.'.php';

        if(!file_exists($compiledView) || !VIEW_CACHE){
            $content = static::compile($view);
            static::file_put_contents($compiledView,static::cleanup($content));
        }

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
        $contentPattern = '#@(\w+)\([\',"](\w+)[\',"]\)(.+?)@end(\w+)#s';
        preg_match_all($contentPattern,$bladeContent,$bladeDirectives,PREG_SET_ORDER);
        //dd($bladeDirectives,false);
        foreach ($bladeDirectives as $bladeDirective) {
            $directive = $bladeDirective[1] ?? '';
            $id = $bladeDirective[2] ?? '';
            $data = $bladeDirective[3] ?? '';
            // $endDirective = $bladeDirective[4] ?? null;
            // if($endDirective && $directive !== $endDirective){
            //     throw new \Exception('@'.$directive.' Invalid view directive!');
            // }
            // $extendPattern= '#@show\([\',"]('.$id.')[\',"]\)#';
            // $extendContent = preg_replace($extendPattern,$data,$extendContent);
            $directives = Directive::directives();
            if(array_key_exists($directive,$directives)){
                $extendContent = $directives[$directive]::render($id,$data,$extendContent);
            }
        }

        //Echo
        $extendContent = preg_replace('#{{(.+?)}}#','<?php echo htmlentities($1); ?>',$extendContent);

        return $extendContent;

    }

    protected static function includes($bladeContent)
    {
        //Includes
        $has_include = preg_match_all('#@include\(\'([a-z,A-Z,0-9,_,-,/,\.]+)\'\)#',$bladeContent,$includes,PREG_SET_ORDER);
        if($has_include){
            //dd($includes);
            foreach($includes as $include){
                $includePath = $include[1];
                $includeContent = static::compile($includePath);
                $bladeContent = preg_replace('#@include\(\'('.$includePath.')\'\)#',$includeContent,$bladeContent);
            }
        }
        return trim($bladeContent,'');
    }

    protected static function extends($bladeContent)
    {
        $extendContent = $bladeContent;
        preg_match('#@extends\(\'([a-z,A-Z,0-9,_,-,/,\.]+)\'\)#',$bladeContent,$extendView);
        if(count($extendView) > 1){
            $extendContent = static::compile($extendView[1]);
            $extendContent = static::includes($extendContent);
        }

        return trim($extendContent,' ');
    }

    protected static function file_put_contents($path,$content){
        //Check If the file Exists
        if(!file_exists($path)){
            $dirname = dirname($path);
            if(!is_dir($dirname)){
                mkdir($dirname);
            }
            $file = fopen($path,'w');
            fwrite($file,$content);
            fclose($file);
        }

        file_put_contents($path,$content,FILE_SKIP_EMPTY_LINES);
    }

    protected static function cleanup($content){
        return preg_replace('#@\w+\(\'\w+\'\)((.+?)?(@end\w+))?#','',$content);
    }    

}