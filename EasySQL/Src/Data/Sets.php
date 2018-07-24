<?php

namespace EasySQL\Src\Data;

class Sets
{

    /**
     * Parameters array ready to be passed to the prepared statement
     *
     * @var $boundParams;
     */
    public $boundParams;

    /**
     * The actions allowed to be performed by the query.
     *
     * @var $actionSet
     */
    public $actionSet;

    /**
     * The options allowed to be performed by the query.
     *
     * @var $options
     */
    public $options;

    /**
     * The operators allowed for the comparison of the where clause of the query
     *
     * @var $operators
     */
    public $operators;

    /**
     * The conditions allowed to connect the where statements of the query
     *
     * @var $conditions
     */
    public $conditions;

    public $dataSets;

    
    public function __construct(array $tables = [])
    {
        
        $this->dataSets = $tables;

        $this->boundParams = [
            'return',
            'options',
            'to_update',
            'updated',
            'options',
        ];

        $this->actionSet   = [
            'insert',
            'update',
            'delete',
            'get',
            'value',
            'INSERT',
            'UPDATE',
            'DELETE',
            'GET',
            'VALUE',
        ];

        $this->options     = [
            'ORDER',
            'LIMIT',
            'order',
            'limit',
        ];

        $this->operators   = [
            'LIKE',
            'like',
            '>',
            '<',
            '<>',
        ];

        $this->conditions  = [
            'AND',
            'OR',
        ];
    }


    /**
     *   Checks if the set provided is one of the database tables
     *
     * @param string $set The name of the Data Set.
     *
     * @return boolean
     *
     * @throws SetException Set Could Not Be Found in the Data Sets.
     */
    public function checkDataSet(string $set)
    {
        if (in_array($set, $this->dataSets) === true) {
            return true;
        } else {
            throw new SetException('The requested '.$set.' data set can not be found.');
        }
    }//end checkDataSet()


    /**
     *   Checks if the action provided is a valid action
     *
     * @param string $action Action to be executed by the api.
     *
     * @return boolean
     *
     * @throws ActionException Action Could Not Be Found in the Action Set.
     */
    public function checkActionSet(string $action)
    {
        if (in_array($action, $this->actionSet) === true) {
            return true;
        } else {
            throw new ActionException('The requested action '.$action.' could not be performed.');
        }
    }//end checkActionSet()

    /**
     * Checks whether the option exists in the options set
     *
     * @param array $params The parameters passed by the user.
     *
     * @return boolean
     */
    public function checkOptions(array $params)
    {
        $flag = false;
        foreach ($params as $option => $value) {
            if (in_array($option, $this->options) === true) {
                $flag = true;
            } else {
                $flag = false;
            }
        }

        return $flag;
    }//end _check_options()
}
