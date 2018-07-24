<?php

namespace EasySQL\Src\Data;

use EasySQL\Src\Collection\Collection as Collection;
use EasySQL\Src\Collection\Group as Group;

class SQL
{

    /**
     * The database connection object
     *
     * @var \PDO $_db
     */
    protected $_db;

    public $sets;

    protected $_parameters;

    public function __construct(\PDO $db)
    {
        $this->_db          = $db;
        $this->sets              = new Sets();
        $this->_parameters = new Parameters($this->_db);
    }


    /**
     * Executes Query to fetch table names
     *
     * @param string $query         The SQL query to be executed.
     * @param array  $params        The parameters array passed by the user.
     *
     * @return array|boolean $data  The resulted database info array from theq query.
     */
    public function fetchDBData(string $query, $params = [])
    {
        // Initialize results array.
        $data = [];

        // Prepare statement and execute it.
        try {
            $stmt = $this->_db->prepare($query);

            $stmt->execute($params);

            // Fetch results as associative arrays and save them in a new array.
            $results = $stmt->fetchAll($this->_db::FETCH_COLUMN, 0);

            foreach ($results as $result) {
                array_push($data, $result);
            }

            $stmt = null;

            return $data;
        } catch (\PDOException $e) {
            echo 'Prepare failed: '.$e->getMessage();
            return false;
        }
    }


    /**
     * Executes a query
     *
     * @param string $query     The SQL query to be executed.
     * @param array  $params    The parameters array passed by the user.
     *
     * @return Collection|string The Collection of the data resulted from the query.
     */
    public function executeQuery(string $query, $params = [])
    {
        $action = strtolower(explode(' ', $query)[0]);
        // Initialize results array.
        $data = [];

        if (empty($params['options']) === true) {
            unset($params['options']);
        }
        
        unset($params['options']);

        // Prepare statement and execute it.
        $stmt = $this->_db->prepare($query);

        $params = $this->_parameters->prepareParameters($query, $params);

        $stmt->execute($params);

        if (in_array($action, $this->sets->actionSet) === true) {
            return 'Query Executed Successfully';
        }

        // Fetch results as associative arrays and save them in a new array.
        $results = $stmt->fetchAll($this->_db::FETCH_ASSOC);

        foreach ($results as $result) {
            array_push($data, $result);
        }

        $stmt = null;

        $collection = new Collection();

        foreach ($data as $item) {
            $collection->addCollection($item);
        }

        return $collection;
    }
}
