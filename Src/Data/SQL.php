<?php

namespace Src\Data;

use Src\Data\DatabaseSingleton as Connection;
use Src\Collection\Collection as Collection;
use Src\Collection\Group as Group;

class SQL
{

    /**
     * The database configuration array
     *
     * @var $config
     */
    protected $config;

    /**
     * The database connection object
     *
     * @var $db
     */
    protected $db;

    public $sets;

    protected $parameters;


    /**
     * Construct the sql part of the api
     *
     * @param array $config The database configuration array.
     *
     * @return void
     */
    public function __construct(array $config)
    {
        $this->_config      = $config;
        $this->_db          = Connection::getDB($this->_config);
        $this->sets              = new Sets();
        $this->_parameters = new Parameters($this->_config);
    }//end __construct()


    /**
     * Executes Query to fetch table names
     *
     * @param string $query  The SQL query to be executed.
     * @param array  $params The parameters passed by the user.
     *
     * @return array $data The resulted database info array from theq query.
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
        }//end try
    }//end fetchDBData()


    /**
     * Executes a query
     *
     * @param string $query  The query to be executed.
     * @param array  $params The parameters passed by the user.
     *
     * @return Collection $collection|string The Collection of the data resulted from the query.
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
    }//end executeQuery()
}//end class
