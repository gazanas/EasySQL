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