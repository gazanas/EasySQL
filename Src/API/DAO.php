<?php

namespace Src\API;

use Src\Data\SQL as SQL;
use Src\Data\QueryFactory as QueryFactory;

class DAO implements DAOInterface
{

    protected $sql;

    protected $table;

    protected $config;

    protected $api;

    protected $queryFactory;

    protected $query;


    public function __construct(array $config, string $table)
    {
        $this->config = $config;
        $this->sql    = new SQL($this->config);
        $this->api    = new API($this->config);

        $this->queryFactory  = new QueryFactory();
        $this->query = $this->queryFactory->getQueryType();

        // Get the table name
        $this->table = $table;
    }


    /**
     *   Returns all the columns of the table
     *
     * @param  array $params (the params that indicates which rows will be returned)
     * @return array
     */
    public function get(array $params = null)
    {
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
     * @return array
     */
    public function value(array $params)
    {
        $required = ['return'];
        // Check if user passed the required "return" parameter
        try {
            $this->api->matchRequiredAction($required, $params, $this->table, $this->config);
        } catch (RequiredException $e) {
            print($e->getMessage());
            return;
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
     */
    public function update(array $params)
    {
        $required = [
            'to_update',
            'updated',
        ];
        try {
            $this->api->matchRequiredAction($required, $params, $this->table, $this->config);
        } catch (RequiredException $e) {
            print($e->getMessage());
            return;
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
     */
    public function delete(array $params = null)
    {
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
     */
    public function insert(array $params)
    {
        $this->query  = $this->queryFactory->getQueryType('insert');

        try {
            $this->api->matchRequired($this->config[4], $params, $this->table, $this->config);
        } catch (RequiredException $e) {
            print($e->getMessage());
            return;
        }

        $query = 'INSERT INTO '.$this->table.' VALUES(';

        $query .= $this->query->setUpInsertQuery($this->api->dbinfo->getColumns($this->table), $params, $this->api->dbinfo->getAutoCompleted($this->table), $this->api->dbinfo->getNullableColumns($this->config[4], $this->table));

        try {
            return $this->sql->executeQuery($query, $params);
        } catch (\PDOException $e) {
            print('Prepare failed: '.$e->getMessage());
            return false;
        }
    }
}
