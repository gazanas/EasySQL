<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data\DAO;
use EasySQL\Src\Sets\Sets;
use EasySQL\Src\Query\GetQuery;
use EasySQL\Src\Query\UpdateQuery;
use EasySQL\Src\Query\DeleteQuery;
use EasySQL\Src\Parameters\WhereParameters;
use EasySQL\Src\Clause\WhereClause;
use EasySQL\Src\Query\InsertQuery;
use EasySQL\Src\Parameters\InsertParameters;
use EasySQL\Src\Clause\OptionsClause;

class API
{

    private $sets;
    private $dao;
    
    /**
     * Constructs the API Object and initializes the essential objects
     * for the API.
     *
     */
    public function __construct(\PDO $database = null)
    {
        $db = (!isset($database)) ? (new Connection())->getDB() : $database;

        $this->sets = new Sets($db);
        
        $this->dao = new DAO($db);
    }

    public function get(string $table, string $join = null, string $onTable = null, string $onJoined = null)
    {
        return new GetQuery($this->dao, new WhereParameters($this->sets), new WhereClause($table, $this->sets), new OptionsClause($this->sets), $table, $join, $onTable, $onJoined);
    }
    
    public function update(string $table)
    {
        return new UpdateQuery($this->dao, new WhereParameters($this->sets), new WhereClause($table, $this->sets), new OptionsClause($this->sets), $table);
    }
    
    public function delete(string $table)
    {
        return new DeleteQuery($this->dao, new WhereParameters($this->sets), new WhereClause($table, $this->sets), new OptionsClause($this->sets), $table);
    }
    
    public function insert(string $table)
    {
        return new InsertQuery($this->dao, new InsertParameters($this->sets), $this->sets, $table);  
    }
}
