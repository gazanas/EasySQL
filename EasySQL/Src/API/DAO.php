<?php

namespace EasySQL\Src\API;

use EasySQL\Src\Data\SQL as SQL;
use EasySQL\Src\Data\QueryFactory as QueryFactory;

class DAO implements DAOInterface
{

    protected $db;
     
    protected $sql;

    protected $table;

    protected $api;

    protected $queryFactory;

    protected $query;


    public function __construct(string $table, \PDO $db)
    {
        $this->db = $db;
        $this->sql    = new SQL($this->db);
        $this->api    = new API();

        $this->queryFactory  = new QueryFactory();

        // Get the table name
        $this->table = $table;
    }


    /**
     *   Returns all the columns of the table
     *
     * @param  array $params (the params that indicates which rows will be returned)
     * @return \EasySQL\Src\Collection\Collection|boolean
     */
    public function get(array $params = null)
    {
        $this->query = $this->queryFactory->getQueryType();

        $query = 'SELECT * FROM '.$this->table;
        if (isset($params) && !empty($params)) {
            $query = $this->query->setUpQuery($query, $params);
        }

        try {
            return $this->sql->executeQuery($query, $params);
        } catch (\PDOException $e) {
            print('Prepare failed: '.$e->getMessage());
            return false;
        }
    }


    /**
     *   Returns a certain column from the rows of the table
     *
     * @param  array $params (the params that indicates which rows will be returned)
     * @return \EasySQL\Src\Collection\Collection|boolean
     */
    public function value(array $params)
    {
        $this->query = $this->queryFactory->getQueryType();

        $required = ['return'];
        // Check if user passed the required "return" parameter
        try {
            $this->api->matchRequiredAction($required, $params, $this->table);
        } catch (RequiredException $e) {
            print($e->getMessage());
            return false;
        }

        // Setup the sql query
        $query  = 'SELECT '.$params['return'].' FROM '.$this->table;
        $return = $params['return'];
        unset($params['return']);
        $query = $this->query->setUpQuery($query, $params);

        // Fetch the query result
        try {
            $result = $this->sql->executeQuery($query, $params);
        } catch (\PDOException $e) {
            print('Prepare failed: '.$e->getMessage());
            return false;
        }

        return $result;
    }


    /**
     *   Updates a column of the table
     *
     * @param array $params (the params that indicates which rows will be returned)
     * 
     * @return string|boolean
     */
    public function update(array $params)
    {
        $this->query = $this->queryFactory->getQueryType();

        $required = [
            'to_update',
            'updated',
        ];
        try {
            $this->api->matchRequiredAction($required, $params, $this->table);
        } catch (RequiredException $e) {
            print($e->getMessage());
            return false;
        }

        $query = 'UPDATE '.$this->table.' SET '.$params['to_update'].' = ?';
        unset($params['to_update']);
        $query = $this->query->setUpQuery($query, $params);

        try {
            return $this->sql->executeQuery($query, $params);
        } catch (\PDOException $e) {
            print('Prepare failed: '.$e->getMessage());
            return false;
        }
    }


    /**
     *   Deletes a row of the table
     *
     * @param array $params (the params that indicates which rows will be returned)
     *
     * @return string|boolean
     */
    public function delete(array $params = null)
    {
        $this->query = $this->queryFactory->getQueryType();
        
        $query = 'DELETE FROM '.$this->table;
        if (isset($params) && !empty($params)) {
            $query = $this->query->setUpQuery($query, $params);
        }

        try {
            return $this->sql->executeQuery($query, $params);
        } catch (\PDOException $e) {
            print('Prepare failed: '.$e->getMessage());
            return false;
        }
    }


    /**
     *   Inserts a new row on the table
     *
     * @param array $params (the params that indicates which rows will be returned)
     *
     * @return string|boolean
     */
    public function insert(array $params)
    {
        $this->query  = $this->queryFactory->getQueryType('insert');

        try {
            $this->api->matchRequired($params, $this->table);
        } catch (RequiredException $e) {
            print($e->getMessage());
            return false;
        }

        $query = 'INSERT INTO '.$this->table.' VALUES(';

        $query .= $this->query->setUpInsertQuery($this->api->dbinfo->getColumns($this->table), $params, $this->api->dbinfo->getAutoCompleted($this->table), $this->api->dbinfo->getNullableColumns($this->table));

        try {
            return $this->sql->executeQuery($query, $params);
        } catch (\PDOException $e) {
            print('Prepare failed: '.$e->getMessage());
            return false;
        }
    }
}
