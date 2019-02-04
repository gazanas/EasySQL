<?php
/**
 * Created by PhpStorm.
 * User: gatas
 * Date: 3/2/2019
 * Time: 10:10 μμ
 */

namespace EasySQL\Src\Data;

class PdoDAO extends DAO
{
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

        for ($i = 0; $i < count($params); $i++) {
            $stmt->bindParam($i+1, $params[$i]);
        }

        $stmt->execute();

        // Fetch results as associative arrays and save them in a new array.
        $data= $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt = null;

        return $data;
    }
}
