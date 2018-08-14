<?php

namespace EasySQL\Src\Data;

<<<<<<< HEAD
=======
use EasySQL\Src\Collection\Collection as Collection;
use EasySQL\Src\Collection\Group as Group;

>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
class SQL
{

    /**
     * The database connection object
     *
     * @var \PDO $_db
     */
<<<<<<< HEAD
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
=======
    protected $_db;

    public $sets;

    protected $_parameters;

    public function __construct(\PDO $db)
    {
        $this->_db          = $db;
        $this->sets              = new Sets();
        $this->_parameters = new Parameters($this->_db);
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
    }


    /**
<<<<<<< HEAD
     * Executes a query
     *
     * @param string $query         The SQL query to be executed.
     * @param array  $params        The parameters array passed by the user.
     * @param int $successString    If the query does not return data return string.
     *
     * @return array|string         The array of the data resulted from the query or a string
     *                               of successfull execution of the query.
     * 
     * @throws \PDOException       
     */
    public function executeQuery(string $query, $params, $successString = null)
    {        
        // Initialize results array.
        $data = array();
=======
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
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
        
        unset($params['options']);

        // Prepare statement and execute it.
<<<<<<< HEAD
        $stmt = $this->db->prepare($query);

        $stmt->execute($params);

        if ($successString == 1) {
=======
        $stmt = $this->_db->prepare($query);

        $params = $this->_parameters->prepareParameters($query, $params);

        $stmt->execute($params);

        if (in_array($action, $this->sets->actionSet) === true) {
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
            return 'Query Executed Successfully';
        }

        // Fetch results as associative arrays and save them in a new array.
<<<<<<< HEAD
        $results = $stmt->fetchAll($this->db::FETCH_ASSOC);
=======
        $results = $stmt->fetchAll($this->_db::FETCH_ASSOC);
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a

        foreach ($results as $result) {
            array_push($data, $result);
        }

        $stmt = null;

<<<<<<< HEAD
        return $data;
=======
        $collection = new Collection();

        foreach ($data as $item) {
            $collection->addCollection($item);
        }

        return $collection;
>>>>>>> d53079c0c8245adc1be698e2fde40a2e8108283a
    }
}
