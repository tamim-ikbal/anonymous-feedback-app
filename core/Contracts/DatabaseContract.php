<?php

namespace Core\Contracts;

interface DatabaseContract{

    public function where(string $column,string $compare='=',$value=null);

    public function orWhere(string $column,string $compare='=',$value=null);

    public function whereNot(string $column,string $compare='=',$value=null);

    public function orWhereNot(string $column,string $compare='=',$value=null);

    public function relations(string $table,string $foreignKey,string $localkey, string $as=null);

    public function get():array;

    public function first():array|null;

    public function firstOrFail():array|null;

    public function count():int;

    public function last_insert():int;

    public function insert(array $data):int;

    public function update(array $data):bool;

    public function delete():bool;
}