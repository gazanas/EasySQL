<?php

namespace EasySQL\Src\Sets;

class Sets extends DatabaseSets {

    protected $db;

    /**
     * Parameters array ready to be passed to the prepared statement
     *
     * @var $boundParams;
     */
    //protected $boundParams;

    protected $actionParameters;

    /**
     * The actions allowed to be performed by the query.
     *
     * @var $actionSet
     */
    protected $actionSet;

    /**
     * The options allowed to be performed by the query.
     *
     * @var $options
     */
    protected $options;

    /**
     * The operators allowed for the comparison of the where clause of the query
     *
     * @var $operators
     */
    protected $operators;

    /**
     * The conditions allowed to connect the where statements of the query
     *
     * @var $conditions
     */
    public $conditions;

    public function __construct($db) {
        
        $this->db = $db;

        $this->actionParameters = [
            'return',
            'to_update',
            'updated'
        ];

        $this->actions   = [
            'insert',
            'update',
            'delete',
            'get',
            'INSERT',
            'UPDATE',
            'DELETE',
            'GET',
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
            '>=',
            '<=',
            '<>',
        ];

        $this->conditions  = [
            'AND',
            'OR',
        ];
    }

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
    }
}
