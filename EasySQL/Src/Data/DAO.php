<?php

namespace EasySQL\Src\Data;

use EasySQL\Src\Query as Query;
use EasySQL\Src\Parameters as Parameters;

class DAO implements DAOInterface
{

    protected $db;
    protected $sql;
    protected $sets;
    protected $table;
    protected $params;
    protected $preparedParameters;
    protected $query;

    /**
    * Initializes the data access object, prepares the parameters and constructs the
    * query for execution on the database.
    *
    * @param Sets $sets         The sets object.
    * @param string $table      The table name.
    * @param string $action     The query action to be executed.
    * @param array $params      The parameters array passed by the user.
    * @param \PDO $db           The database object. 
    */
    public function __construct($sets, string $table, string $action, $params, $db)
    {
        $this->db = $db;

        $this->sql    = new SQL($this->db);

        $this->sets = $sets;

        $this->table = $table;

        $this->params = $params;

        $parametersObject = new Parameters\Parameters($this->sets);

        $this->preparedParameters = $parametersObject->prepareParameters($action, $this->table, $this->params);

        $queryObject = new Query\Query();
        
        $this->query = $queryObject->setUpQuery($this->sets, $action, $this->table, $this->params);
        
    }


    /**
     *   Returns all the columns of the table
     *
     * @return array
     */
    public function get()
    {        
        return $this->sql->executeQuery($this->query, $this->preparedParameters);
    }


    /**
     *   Returns a certain column from the rows of the table
     *
     * @return array
     */
    public function value()
    {
        // Fetch the query result
        return $this->sql->executeQuery($this->query, $this->preparedParameters);
    }


    /**
     *   Updates a column of the table
     *
     * @return string
     */
    public function update()
    {
        $getDAO = new self($this->sets, $this->table, 'get', array_diff_key($this->params, array_flip(array('to_update', 'updated'))), $this->db);
        if(empty((array)$getDAO->get()))
            throw new \Exception('The row you are trying to update does not exist.');
        
        return $this->sql->executeQuery($this->query, $this->preparedParameters, 1);
    }

    /**
     *   Deletes a row of the table
     *
     * @return string
     */
    public function delete()
    {
        $getDAO = new self($this->sets, $this->table, 'get', array_diff_key($this->params, array_flip(array('return'))), $this->db);
        if(empty((array)$getDAO->get()))
            throw new \Exception('The row you are trying to delete does not exist.');

        return $this->sql->executeQuery($this->query, $this->preparedParameters, 1);
    }


    /**
     *   Inserts a new row on the table
     *
     * @return string
     */
    public function insert()
    {
        return $this->sql->executeQuery($this->query, $this->preparedParameters, 1);
    }
}
