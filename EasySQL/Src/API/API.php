<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data as Data;

class API
{
    protected $db;
    protected $sets;
    
    /**
     * Constructs the API Object and initializes the essential objects
     * for the API.
     *
     * @param PDO $db   The database object (it is only passed for unit testing)
     */
    public function __construct(\PDO $db)
    {
        $this->db = $db;
        $this->sets = new Data\Sets($this->db);
    }


    /**
     * Gets the api call input, constructs a call
     * to the associated data access object (DAO),
     * and returns the result of the call.
     *
     * @param string $set      Table name.
     * @param string $action   Action to perform.
     * @param array  $params   The parameters array passed by the user.
     *
     * @return mixed $data      Result Of The Performed Action.
     **/
    public function _easy_sql(string $set, string $action, array $params = null)
    {
        $this->checkDataSet($set);

        $this->checkActionSet($action);

        $object          = new Data\DAO($this->sets, $set, strtolower($action), $params, $this->db);
        $data            = $object->$action();
        return $data;
    }

        /**
     *   Checks if the set provided is one of the database tables
     *
     * @param string $set   The name of the Data Set.
     *
     * @return boolean
     *
     * @throws SetException Set Could Not Be Found in the Data Sets.
     */
    private function checkDataSet(string $set) {
        
        if (in_array($set, $this->sets->getTables()) === true) {
            return true;
        } else {
            throw new \Exception('The requested '.$set.' data set can not be found.');
        }
    }


    /**
     *   Checks if the action provided is a valid action
     *
     * @param string $action    Action to be executed by the api.
     *
     * @return boolean
     *
     * @throws ActionException Action Could Not Be Found in the Action Set.
     */
    private function checkActionSet(string $action)
    {
        if (in_array($action, $this->sets->getActionSet()) === true) {
            return true;
        } else {
            throw new \Exception('The requested action '.$action.' could not be performed.');
        }
    }
}