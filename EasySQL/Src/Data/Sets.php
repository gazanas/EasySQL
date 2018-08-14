<?php

namespace EasySQL\Src\Data;

<<<<<<< HEAD
class Sets extends DatabaseSets
{

    protected $db;

=======
class Sets
{

>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
    /**
     * Parameters array ready to be passed to the prepared statement
     *
     * @var $boundParams;
     */
<<<<<<< HEAD
    //protected $boundParams;

    protected $actionParameters;
=======
    public $boundParams;
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    /**
     * The actions allowed to be performed by the query.
     *
     * @var $actionSet
     */
<<<<<<< HEAD
    protected $actionSet;
=======
    public $actionSet;
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    /**
     * The options allowed to be performed by the query.
     *
     * @var $options
     */
<<<<<<< HEAD
    protected $options;
=======
    public $options;
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    /**
     * The operators allowed for the comparison of the where clause of the query
     *
     * @var $operators
     */
<<<<<<< HEAD
    protected $operators;
=======
    public $operators;
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

    /**
     * The conditions allowed to connect the where statements of the query
     *
     * @var $conditions
     */
    public $conditions;

<<<<<<< HEAD
    public function __construct($db)
    {
        
        $this->db = $db;

        /*$this->boundParams = [
=======
    public $dataSets;

    
    public function __construct(array $tables = [])
    {
        
        $this->dataSets = $tables;

        $this->boundParams = [
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
            'return',
            'options',
            'to_update',
            'updated',
            'options',
<<<<<<< HEAD
        ];*/

        $this->actionParameters = [
            'return',
            'to_update',
            'updated'
        ];

        $this->actions   = [
=======
        ];

        $this->actionSet   = [
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
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
<<<<<<< HEAD
            '>=',
            '<=',
=======
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
            '<>',
        ];

        $this->conditions  = [
            'AND',
            'OR',
        ];
    }

<<<<<<< HEAD
    /**
    * Returns an array with the parameters that can't be used
    * as column names.
    *
    * @return array $this->boundParams  The array of the boundParams.
    *
    public function getBoundParams() {
        return $this->boundParams;
    }
    */
    
    /**
    * Returns an array containing the action parameters.
    *
    * @return array $this->actionParameters
    */
    public function getActionParameters() {
        return $this->actionParameters;
    }

    /**
    * Returns an array with the acceptable actions.
    *
    * @return array $this->actionSet
    */
    public function getActionSet() {
        return $this->actions;
    }

    /**
    * Returns an array with the acceptable options.
    *
    * @return array $this->options;
    */
    public function getOptionSet() {
        return $this->options;
    }

    /**
    * Returns an array with the acceptable operators.
    *
    * @return array $this->operators
    */
    public function getOperatorSet() {
        return $this->operators;
    }

    /**
    * Returns an array with the acceptable conditions.
    *
    * @return array $this->conditions
    */
    public function getConditionSet() {
        return $this->conditions;
=======

    /**
     *   Checks if the set provided is one of the database tables
     *
     * @param string $set   The name of the Data Set.
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
    public function checkActionSet(string $action)
    {
        if (in_array($action, $this->actionSet) === true) {
            return true;
        } else {
            throw new ActionException('The requested action '.$action.' could not be performed.');
        }
    }

    /**
     * Checks whether the option exists in the options set
     *
     * @param array $params     The parameters array passed by the user.
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
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
    }
}
