<?php

namespace EasySQL\Src\Data;

class SQL
{

    /**
     * The database connection object
     *
     * @var \PDO $_db
     */
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    /**
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
        
        unset($params['options']);

        // Prepare statement and execute it.
        $stmt = $this->db->prepare($query);

        $stmt->execute($params);

        if ($successString == 1) {
            return 'Query Executed Successfully';
        }

        // Fetch results as associative arrays and save them in a new array.
        $results = $stmt->fetchAll($this->db::FETCH_ASSOC);

        foreach ($results as $result) {
            array_push($data, $result);
        }

        $stmt = null;

        return $data;
    }
}
