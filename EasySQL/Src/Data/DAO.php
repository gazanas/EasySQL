<<<<<<< HEAD
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
=======
<?php

namespace EasySQL\Src\Data;

use EasySQL\Src\API as API;
use EasySQL\Src\Query as Query;
use EasySQL\Src\Parameters as Parameters;
use EasySQL\Src\Sets as Sets;

class DAO implements DAOInterface {

    private $sql;
    private $sets;
    private $table;
    private $params;
    private $preparedParameters;
    private $query;

    /**
    * Initializes the data access object, prepares the parameters and constructs the
    * query for execution on the database.
    *
    * @param SQL $sql            The sql object. 
    * @param Sets\Sets $sets     The sets object.
    * @param string $table       The table name.
    * @param string $action      The query action to be executed.
    */
    public function __construct(SQL $sql, Sets\Sets $sets, Parameters\Parameters $parameters, Query\Query $query, string $table, array $params) {
        
        $this->sql = $sql;

        $this->sets = $sets;

        $this->table = $table;

        $this->params = $params;

        $this->preparedParameters = $parameters->prepareParameters($this->table, $this->params);

        $this->query = $query->setUpQuery($sets, $this->table, $this->params);
        
    }


    /**
     *   Returns all the columns of the table
     *
     * @return array
     */
    public function get() { 
        return $this->sql->executeQuery($this->query, $this->preparedParameters);
    }

    /**
     *   Updates a column of the table
     *
     * @return string
     */
    public function update() {
        $api = new API\API($this->sets);   
        if(empty($api->_easy_sql($this->sql, $this->table, 'get', array_diff_key($this->params, array('to_update' => 1, 'updated' => 1)))))
            throw new \Exception('The row you are trying to update doesn\'t exist.');  
        return $this->sql->executeQuery($this->query, $this->preparedParameters, 1);
    }

    /**
     *   Deletes a row of the table
     *
     * @return string
     */
    public function delete() {
        $api = new API\API($this->sets);   
        if(empty($api->_easy_sql($this->sql, $this->table, 'get', $this->params)))
            throw new \Exception('The row you are trying to delete doesn\'t exist.');  
        return $this->sql->executeQuery($this->query, $this->preparedParameters, 1);
    }


    /**
     *   Inserts a new row on the table
     *
     * @return string
     */
    public function insert() {
        return $this->sql->executeQuery($this->query, $this->preparedParameters, 1);
    }
}
>>>>>>> f1d508c7fe88400367650b9c0be5ef42d7bd4b13
