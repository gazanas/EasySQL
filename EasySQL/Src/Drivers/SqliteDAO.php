<?php
/**
 * Created by PhpStorm.
 * User: gatas
 * Date: 3/2/2019
 * Time: 10:10 μμ
 */

namespace EasySQL\Drivers;

class SqliteDAO extends DAO
{

    /**
     * The database object
     * 
     * @var Pdo | Mysqli
     */
    protected $db;

    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Executes a query
     *
     * @param string $query
     * @param array  $params
     *
     * @return array
     */
    public function executeQuery(string $query, array $params)
    {
        // Prepare statement and execute it.
        $stmt = $this->db->prepare($query);

        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindParam($i+1, $params[$i]);
        }

        $stmt->execute();

        // Fetch results as associative arrays and save them in a new array.
        $data = $stmt->fetchAll();

        $stmt = null;

        return $data;
    }
}
