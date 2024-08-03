<?php

namespace Core;

use Core\Contracts\DatabaseContract;

class DB{

    public DatabaseContract $database;

    public static function table($tableName):DB
    {

        //Create An Instance
        $instance =  new self();

        $instance->database = new FileDatabase($tableName);
               
        return $instance;

    }

    public function where(string $column,string $compare='=',$value=null):DB
    {
        $this->database->where($column, $compare, $value);
        return $this;
    }

    public function orWhere(string $column,string $compare='=',$value=null):DB
    {
        $this->database->orWhere($column, $compare, $value);
        return $this;
    }

    public function whereNot(string $column,string $compare='=',$value=null):DB
    {
        $this->database->whereNot($column, $compare, $value);
        return $this;
    }

    public function orWhereNot(string $column,string $compare='=',$value=null):DB
    {
        $this->database->orWhereNot($column, $compare, $value);
        return $this;
    }

    public function relations(string $table,string $foreignKey,string $localkey, string $as=null):DB
    {
        $this->database->relations($table,$foreignKey,$localkey,$as);
        return $this;
    }

    public function get():array
    {
        return $this->database->get();
    }

    public function first():array|null
    {
        return $this->database->first();
    }

    public function firstOrFail():array|null
    {
        return $this->database->firstOrFail();
    }

    public function count():int
    {
        return $this->database->count();
    }

    public function last_insert():int
    {
        return $this->database->last_insert();
    }

    public function insert(array $data):int
    {
        return $this->database->insert($data);
    }

    public function update(array $data):bool
    {
        return $this->database->update($data);
    }

    public function delete():bool
    {
        return $this->database->delete();
    }

}