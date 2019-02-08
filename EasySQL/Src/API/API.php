<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data\DAO;
use EasySQL\Src\Data\DAOFactory;
use EasySQL\Src\Sets\Sets;
use EasySQL\Src\Query\GetQuery;
use EasySQL\Src\Query\UpdateQuery;
use EasySQL\Src\Query\DeleteQuery;
use EasySQL\Src\Parameters\WhereParameters;
use EasySQL\Src\Clause\WhereClause;
use EasySQL\Src\Query\InsertQuery;
use EasySQL\Src\Parameters\InsertParameters;
use EasySQL\Src\Clause\OptionsClause;
use EasySQL\Src\Clause\GroupClause;

class API
{

    private $sets;
    private $dao;

    /**
     * Constructs the API Object and initializes the essential objects
     * for the API.
     */
    public function __construct(String $driver, \PDO $db = null)
    {
        $createConnection = 'create'.ucfirst(strtolower(($driver)));
        $db = (isset($db)) ? $db : (new ConnectionFactory())->$createConnection();

        $createDAO = 'create'.ucfirst(strtolower(($driver))).'DAO';
        $this->dao = (new DAOFactory())->$createDAO($db);

        $this->sets = new Sets($this->dao);
    }

    public function get(string $table)
    {
        return new GetQuery(
            $this->dao,
            new WhereParameters($this->sets),
            new WhereClause(),
            new OptionsClause($this->sets),
            $table,
            $this->sets,
            new GroupClause($table, $this->sets)
        );
    }
    
    public function update(string $table)
    {
        return new UpdateQuery(
            $this->dao,
            new WhereParameters($this->sets),
            new WhereClause(),
            new OptionsClause($this->sets),
            $table,
            $this->sets
        );
    }
    
    public function delete(string $table)
    {
        return new DeleteQuery(
            $this->dao,
            new WhereParameters($this->sets),
            new WhereClause(),
            new OptionsClause($this->sets),
            $table,
            $this->sets
        );
    }
    
    public function insert(string $table)
    {
        return new InsertQuery($this->dao, new InsertParameters($this->sets), $this->sets, $table);
    }
}
