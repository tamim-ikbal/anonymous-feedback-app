<?php

namespace Core;

class Validator{

    protected array $validationRules = [
        'required',
        'string',
        'email',
        'number',
        'escape',
        'min',
        'max',
        'lenght',
        'same',
    ];

    protected bool $isFailed    = false;
    protected array $rules      = [];
    protected array $messages   = [];
    protected array $errors     = [];
    protected array $data     = [];
    protected Request $request;

    public static function make(Request $request,$rules,$messages=[])
    {
        $instance = new Static();
        //Messages
        $instance->messages = array_merge($instance->defaultMessages(),$messages);
        //Assign Request
        $instance->request = $request;
        //Assign Rules
        $instance->rules = $rules;

        return $instance;
    }

    public function valiated():bool
    {
        foreach($this->rules as $name => $rules){
            $input = $this->request->input($name);
            //If Escape
            if(in_array('escape',$rules)){
                $input = $this->escape($input);
                unset($rules['escape']);
            }
            foreach($rules as $rule){
                $explode = explode(':',$rule,2);
                $rule = $explode[0];
                $reqValue = $explode[1] ?? '';
                $value = $explode[1] ?? '';
                if($rule === 'same'){
                    $reqValue = $this->request->input($value);
                }

                //Check is the rule is valid
                if(!in_array($rule,$this->validationRules)){
                    continue;
                }

                $passed = call_user_func([$this,$rule],$input,$reqValue);
                if(!$passed){
                    $this->errors[$name][] = $this->message($rule,$name,$value);
                    $this->isFailed = true;
                }
            } //Validation

            $this->data[$name] = $input;

        }
        
        return !$this->isFailed;
    }

    public function errors():array
    {
        return $this->errors;
    }

    public function data():array
    {
        return $this->data;
    }

    public function messages($messages)
    {
        //
    }

    protected function defaultMessages()
    {
        return [
            'required'  => 'The :name field is required.',
            'string'    => 'The :name field must be a string.',
            'email'     => 'The :name field must be an email.',
            'number'    => 'The :name field must be a number.',
            'escape'    => 'The :name field is failed to escape.',
            'min'       => 'The :name field must be greater than :value',
            'max'       => 'The :name field must be less than :value',
            'lenght'    => 'The :name field must be equal to :value',
            'same'      => 'The :name field must be same as :value',
        ];
    }

    protected function message($rule,$name,$value='')
    {
        $name = ucwords(str_replace(['-','_'],[' ',' '],$name));
        return str_replace([':name',':value'],[$name,$value],$this->messages[$rule]);
    }

    protected function required($input)
    {
        return !empty($input);
    }

    protected function string($input)
    {
        return is_string($input);
    }

    protected function email($input)
    {
        return filter_var($input,FILTER_VALIDATE_EMAIL);
    }
    
    protected function number($input)
    {
        return is_numeric($input);
    }

    protected function min(int $input,int $value)
    {
        return $input >= $value;
    }

    protected function max(int $input,int $value)
    {
        return $value >= $input;
    }

    protected function lenght(int $input,int $value)
    {
        return $value === strlen($input);
    }

    protected function same($input,$value)
    {
        return $value === $input;
    }

    protected function escape($input)
    {
        return $input ? htmlspecialchars(htmlentities($input)) : null;
    }

}