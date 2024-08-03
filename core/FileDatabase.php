<?php

namespace Core;

use Core\Contracts\DatabaseContract;

class FileDatabase implements DatabaseContract{

    
    private $entries = [];
    private $data = [];
    private $table;
    private $dbUrl;

    public function __construct($tableName)
    {
        //DB Url
        $this->dbUrl = base_path('database/');
        
         //Assign Table 
         $this->table = $this->dbUrl.$tableName.'.json';
 
         //Check if table exists
         if(!$this->isTableExist()){
             throw new \Exception('Table doesn\'t Exists');
         }
 
         //Setup ENV
         $this->setupEnv();
    }

    public function where($column,$compare='=',$value=null){
        $this->data = array_filter($this->data,function($item) use ($column,$compare,$value){
            if(!isset($item[$column])){
                return false;
            }
            return $this->compare($item[$column],$compare,$value);
        });    
        return $this;
    }

    public function orWhere($column,$compare='=',$value=null){
        $data = array_filter($this->entries,function($item) use ($column,$compare,$value){
            if(!isset($item[$column])){
                return false;
            }
            return $this->compare($item[$column],$compare,$value);
        });

        $this->data = array_merge($this->data,$data);

        return $this;
    }

    public function whereNot($column,$compare='=',$value=null){
        $this->data = array_filter($this->data,function($item) use ($column,$compare,$value){
            if(!isset($item[$column])){
                return false;
            }
            return !$this->compare($item[$column],$compare,$value);
        });

        return $this;
    }

    public function orWhereNot($column,$compare='=',$value=null){
        $data = array_filter($this->entries,function($item) use ($column,$compare,$value){
            if(!isset($item[$column])){
                return false;
            }
            return !$this->compare($item[$column],$compare,$value);
        });

        $this->data = array_merge($this->data,$data);

        return $this;
    }

    public function get():array
    {
        //return array_map(fn($item)=> new stdClass($item),$this->data);
        return $this->data;
    }

    public function first():array|null {
        $key = array_key_first($this->get());

        if(!isset($this->get()[$key])){
            return null;
        }
        return $this->get()[$key];
    }

    public function firstOrFail():array|null {
        $first = $this->first();

        if(!$first){
            abort(404);
        }

        return $first;
    }

    public function count():int
    {
        return count($this->data);
    }
    
    public function last_insert():int {
        $read = $this->read();

        if(!isset($read['last_insert'])){
            return 0;
        }

        return (int) $read['last_insert'];
    }

    public function relations($table,$foreignKey,$localkey,$as=null) {
        if(!$as){
            $as = $table;
        }
        $realtionEntries = DB::table($table)->get();
        
        $this->data = array_map(function($item) use($realtionEntries,$foreignKey,$localkey,$as){
            // print_r($item);
            // print_r(array_key_exists($foreignKey ,$item));
            // die();
            if(!array_key_exists($foreignKey ,$item) || !$item[$foreignKey]){
                return $item;
            }

            foreach ($realtionEntries as $key => $entry) {
                if($entry[$localkey] == $item[$foreignKey]){
                    //
                    if(isset($entry['password'])){
                        unset($entry['password']);
                    }
                    $item[$as]= $entry;
                    break;
                }
            }

            return $item;

        },$this->data);

        return $this;

    }

    public function insert(array $data):int{

        //Get Entries And Last Insert ID
        $read = $this->read();
        $last_insert = $read['last_insert'] ?? 0;
        $entries = $read['entries'] ?? [];

        //Auto Increament ID
        $insert_id = (int) $last_insert + 1;
        $data['id'] = $insert_id;

        //Insert new Data
        $entries[]=$data;
        
        //Schema
        $schema = $this->buildDBSchema($entries,$insert_id);
    
        $this->write($schema);

        return $insert_id;
    }

    public function update(array $data):bool{

        //Get Entries And Last Insert ID
        $read = $this->read();
        $last_insert = $read['last_insert'] ?? 0;
        $entries = $read['entries'] ?? [];

        //Updatable IDs
        $updatable_ids = array_map(fn($item)=> (int) $item['id'],$this->get());

        //Make sure Id is not going to update.
        if(isset($data['id'])){
            unset($data['id']);
        }

        $updated_entries = array_map(function($entry) use ($updatable_ids,$data){
            if(in_array($entry['id'],$updatable_ids)){
                return array_merge($entry,$data);
            }
            return $entry;
        },$entries);

        //Schema
        $schema = $this->buildDBSchema($updated_entries,$last_insert);
    
        $this->write($schema);

        return true;
    }

    public function delete():bool{
        //Get Entries And Last Insert ID
        $read = $this->read();
        $last_insert = $read['last_insert'] ?? 0;
        $entries = $read['entries'] ?? [];

        //Updatable IDs
        $deletable_ids = array_map(fn($item)=> (int) $item['id'],$this->get());

        $updated_entries = array_filter($entries,function($entry) use($deletable_ids){
            if(in_array($entry['id'],$deletable_ids)){
                return false;
            }
            return true;
        });

        //Schema
        $schema = $this->buildDBSchema($updated_entries,$last_insert);

        $this->write($schema);

        return true;
    }

    private function getData():array{
        // $data = [
        //     [
        //         'id'   => 1,
        //         'name' => 'Tamim',
        //         'age'  => '25'
        //     ],
        //     [
        //         'id'   => 2,
        //         'name' => 'Radif',
        //         'age'  => '20'
        //     ],
        //     [
        //         'id'   => 3,
        //         'name' => 'Tanjil',
        //         'age'  => '20'
        //     ],
        //     [
        //         'id'   => 4,
        //         'name' => 'Sajib',
        //         'age'  => '25'
        //     ],
        //     [
        //         'id'   => 5,
        //         'name' => 'Saiful',
        //         'age'  => '20'
        //     ],
        //     [
        //         'id'   => 6,
        //         'name' => 'Porag',
        //         'age'  => '17'
        //     ],
        //     [
        //         'id'   => 7,
        //         'name' => 'Minhaj',
        //         'age'  => '17'
        //     ],
        //     [
        //         'id'   => 8,
        //         'name' => 'Sumon',
        //         'age'  => '25'
        //     ],
        //     [
        //         'id'   => 9,
        //         'name' => 'Mahbub',
        //         'age'  => '18'
        //     ],
        //     [
        //         'id'   => 10,
        //         'name' => 'Rajib',
        //         'age'  => '24'
        //     ],
        // ];

        $read = $this->read();

        if(!isset($read['entries'])){
            return [];
        }

        return $read['entries'];
    }

    private function compare($column,$compare,$value):bool{
        switch ($compare) {
            case '=':
            case '==':
            case '===':
                return $column == $value;
            case '!=':
            case '!==':
                return $column != $value;
            case '>':
                return $column > $value;
            case '>=':
                return $column >= $value;
            case '<':
                return $column < $value;
            case '<=':
                return $column <= $value;        
            default:
                return false;
        }
    }

    private function isTableExist(){
        return file_exists($this->table);
    }

    private function setupEnv() {
        $this->entries = $this->getData();
        $this->data = $this->getData();
    }

    private function read(){
        $data = file_get_contents($this->table);

        return json_decode($data,true);
    }

    private function write(array $schema){
        file_put_contents($this->table,json_encode($schema,JSON_PRETTY_PRINT));
        return true;
    }

    private function buildDBSchema(array $entries,int $last_insert){
        return [
            'last_insert' => $last_insert,
            'entries' => $entries
        ];
    }
    
}