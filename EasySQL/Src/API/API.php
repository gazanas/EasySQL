<<<<<<< HEAD
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
=======
<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data as Data;
use EasySQL\Src\Parameters as Parameters;
use EasySQL\Src\Sets as Sets;

class API {

    private $sets;
    
    /**
     * Constructs the API Object and initializes the essential objects
     * for the API.
     *
     * @param Sets\Sets $sets                       The sets object.
     *
    */
    public function __construct(Sets\Sets $sets) {

        $this->sets = $sets;
    }


    /**
     * Gets the api call input, constructs a call
     * to the associated data access object (DAO),
     * and returns the result of the call.
     *
     * @param Data\SQL $sql     The sql object.
     * @param string $table     Table name.
     * @param string $action    Action to perform.
     * @param array  $params    The parameters array passed by the user.
     *
     * @return array $data      Result Of The Performed Action.
     **/
    public function _easy_sql(Data\SQL $sql, $table, $action, $params = null) {
        $action = strtolower($action);

        $this->checkDataSet($table);
        $this->checkActionSet($action);
        $this->checkParamsType($params);
            
        $queryObject = 'EasySQL\\Src\\Query\\'.ucfirst(strtolower($action)).'Query';
        $query = new $queryObject();

        $parameterObject = 'EasySQL\\Src\\Parameters\\'.ucfirst(strtolower($action)).'Parameters';
        $parameters = new $parameterObject($this->sets);

        $object = new Data\DAO($sql, $this->sets, $parameters, $query, $table, $params);
        $data = $object->$action();
    
        return $data;
    }

    /**
     * Checks if the set provided is one of the database tables
     *
     * @param string $set   The name of the Data Set.
     *
     * @throws SetException Set Could Not Be Found in the Data Sets.
     */
    private function checkDataSet($table) {
        if(!is_string($table))
            throw new \Exception('The data set should be of type string.');

        if (!in_array($table, $this->sets->getTables(), true))
            throw new \Exception('The requested '.$table.' data set can not be found.');
    }


    /**
     * Checks if the action provided is a valid action
     *
     * @param string $action    Action to be executed by the api.
     *
     * @throws ActionException Action Could Not Be Found in the Action Set.
     */
    private function checkActionSet($action) {
        if(!is_string($action))
            throw new \Exception('The action should be of type string.');

        if (!in_array($action, $this->sets->getActionSet(), true))
            throw new \Exception('The requested action '.$action.' could not be performed.');
    }

    private function checkParamsType($params) {
        if(!is_array($params))
            throw new \Exception('Parameters should be of type array.');
    }
}
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
