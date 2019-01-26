<?php

namespace EasySQL\Src\Sets;

<<<<<<< HEAD:EasySQL/Src/Sets/Sets.php
class Sets extends DatabaseSets
{
=======
class Sets extends DatabaseSets {
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13:EasySQL/Src/Sets/Sets.php

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

<<<<<<< HEAD:EasySQL/Src/Sets/Sets.php
    public function __construct($db)
    {
=======
    public function __construct($db) {
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13:EasySQL/Src/Sets/Sets.php
        
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
<<<<<<< HEAD:EasySQL/Src/Sets/Sets.php
     * Returns an array containing the action parameters.
     *
     * @return array $this->actionParameters
     */
    public function getActionParameters()
    {
=======
    * Returns an array containing the action parameters.
    *
    * @return array $this->actionParameters
    */
    public function getActionParameters() {
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13:EasySQL/Src/Sets/Sets.php
        return $this->actionParameters;
    }

    /**
<<<<<<< HEAD:EasySQL/Src/Sets/Sets.php
     * Returns an array with the acceptable actions.
     *
     * @return array $this->actionSet
     */
    public function getActionSet()
    {
=======
    * Returns an array with the acceptable actions.
    *
    * @return array $this->actionSet
    */
    public function getActionSet() {
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13:EasySQL/Src/Sets/Sets.php
        return $this->actions;
    }

    /**
<<<<<<< HEAD:EasySQL/Src/Sets/Sets.php
     * Returns an array with the acceptable options.
     *
     * @return array $this->options;
     */
    public function getOptionSet()
    {
=======
    * Returns an array with the acceptable options.
    *
    * @return array $this->options;
    */
    public function getOptionSet() {
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13:EasySQL/Src/Sets/Sets.php
        return $this->options;
    }

    /**
<<<<<<< HEAD:EasySQL/Src/Sets/Sets.php
     * Returns an array with the acceptable operators.
     *
     * @return array $this->operators
     */
    public function getOperatorSet()
    {
=======
    * Returns an array with the acceptable operators.
    *
    * @return array $this->operators
    */
    public function getOperatorSet() {
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13:EasySQL/Src/Sets/Sets.php
        return $this->operators;
    }

    /**
<<<<<<< HEAD:EasySQL/Src/Sets/Sets.php
     * Returns an array with the acceptable conditions.
     *
     * @return array $this->conditions
     */
    public function getConditionSet()
    {
=======
    * Returns an array with the acceptable conditions.
    *
    * @return array $this->conditions
    */
    public function getConditionSet() {
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13:EasySQL/Src/Sets/Sets.php
        return $this->conditions;
    }
}
