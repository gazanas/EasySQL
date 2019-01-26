<?php

namespace EasySQL\Src\Data;

class DAO
{

    /**
     * The database connection object
     *
     * @var \PDO $_db
     */
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }


    /**
     * Executes a query
     *
     * @param string $query         The SQL query to be executed.
     * @param array  $params        The parameters array passed by the user.
     * @param int    $successString If the query does not return data return string.
     *
     * @return array|string         The array of the data resulted from the query or a string
     *                               of successfull execution of the query.
     * 
     * @throws \PDOException       
     */
    public function executeQuery(string $query, array $params)
    {   
        // Prepare statement and execute it.
        $stmt = $this->db->prepare($query);
        
        for($i = 0; $i < count($params); $i++)
            $stmt->bindParam($i+1, $params[$i]);

        $stmt->execute();

        // Fetch results as associative arrays and save them in a new array.
        $data= $stmt->fetchAll($this->db::FETCH_ASSOC);

        $stmt = null;

        return $data;
    }
}
