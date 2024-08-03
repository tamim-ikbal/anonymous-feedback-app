<?php

namespace Core;

defined('ABSPATH') || die('You are not allowed');

class Request{

    protected string $redirectTo;
    private array $server;
    private array $post;
    private array $get;
    private array $requests;

    public function __construct()
    {
        $this->server = $_SERVER;
        $this->post = $_POST;
        $this->get = $_GET;
        $this->requests = array_merge($this->post,$this->get);

        //Do Validation
        $prevUrl = parse_url($this->server['HTTP_REFERER'] ?? '');
        $this->redirectTo =  $prevUrl['path'].(isset($prevUrl['query']) ? '?'.$prevUrl['query']:'');
    }

    public function all(){
        return $this->requests;
    }

    public function input($key,$default=null){
        return $this->requests[$key] ?? $default;
    }

    public function query($key,$default=null){
        return $this->get[$key] ?? $default;
    }

    public function validated()
    {
        if(0 >= count($this->rules())){
            return array_map('htmlspecialchars',$this->all());
        }
        $validator = Validator::make($this,$this->rules(),$this->messages());

        //Validate or not
        if(!$validator->valiated()){
            return redirect($this->redirectTo,[
                'errors'    => $validator->errors(),
                'old'       => array_map('htmlspecialchars',$this->all()),
            ]);
        }
        
        return $validator->data();
    }

    protected function rules():array
    {
        return [];
    }
    protected function messages():array
    {
        return [];
    }
}